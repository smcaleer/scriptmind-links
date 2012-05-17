<?php
/**
# ######################################################################
# Project:     PHPLinkDirectory: Version 2.1.2
#
# **********************************************************************
# Copyright (C) 2004-2006 NetCreated, Inc. (http://www.netcreated.com/)
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
# **********************************************************************
#
# For questions, help, comments, discussion, etc., please join the
# PHP Link Directory Forum http://www.phplinkdirectory.com/forum/
#
# @link           http://www.phplinkdirectory.com/
# @copyright      2004-2006 NetCreated, Inc. (http://www.netcreated.com/)
# @projectManager David DuVal <david@david-duval.com>
# @package        PHPLinkDirectory
# ######################################################################
*/

require_once 'init.php';

$id = ( isset ($_REQUEST['id']) ? trim ($_REQUEST['id']) : (isset ($_REQUEST['ID']) ? trim ($_REQUEST['ID']) : 0) );

$id = preg_replace ('`(id[_]?)`', '', $id);
$id = (preg_match ('`^[\d]+$`', $id) ? intval ($id) : 0);

if (empty ($_REQUEST['submit']))
{
   if (!empty ($_SERVER['HTTP_REFERER']))
      $_SESSION['return'] = $_SERVER['HTTP_REFERER'];

   if (!empty ($id))
   {
      if ($data = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($id)))
      {
         if (empty ($data['RECPR_URL']))
         {
            $_SESSION['cid'] = $data['CATEGORY_ID'];
            SmartyValidate :: connect($tpl);
            SmartyValidate :: register_form('add_reciprocal', true);
            SmartyValidate :: register_criteria('isRecprOnline'  , 'validate_recpr_link', 'add_reciprocal');
            SmartyValidate :: register_validator('v_RECPR_URL'   , 'RECPR_URL', 'isURL'        , !$recpr_required, false, 'trim', "add_reciprocal");
            SmartyValidate :: register_validator('v_RECPR_ONLINE', 'RECPR_URL', 'isRecprOnline', !$recpr_required, false, null, "add_reciprocal");
         }
         else
            $tpl->assign('link_id_error', 'Reciprocal link is already defined for this link.');
      }
      else
         $tpl->assign('link_id_error', 'Please ensure that the URL is complete.');
   }
   else
      $tpl->assign('link_id_error', 'Please ensure that the URL is complete.');
}
else
{
   SmartyValidate :: connect($tpl);
   if ($data = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($id)))
   {
      $data['IPADDRESS'] = get_client_ip();

      $data['RECPR_URL'] = $_REQUEST['RECPR_URL'];
      $data['VALID'] = 1;
      if ($data['RECPR_REQUIRED'])
      {
         $data['RECPR_VALID'] = 1;
         $data['RECPR_LAST_CHECKED'] = gmdate ('Y-m-d H:i:s');
      }
      $data['LAST_CHECKED']  = gmdate ('Y-m-d H:i:s');
      //$data['DATE_ADDED']    = gmdate ('Y-m-d H:i:s');
      unset ($data['EXPIRY_DATE']);
      $data['DATE_MODIFIED'] = gmdate ('Y-m-d H:i:s');
      if (strlen (trim ($data['URL'])) > 0 && !preg_match ('#^http[s]?:\/\/#i', $data['URL']))
         $data['URL'] = "http://".$data['URL'];

      if (strlen (trim ($data['RECPR_URL'])) > 0 && !preg_match ('#^http[s]?:\/\/#i', $data['RECPR_URL']))
         $data['RECPR_URL'] = "http://".$data['RECPR_URL'];

      if (SmartyValidate :: is_valid($data, "add_reciprocal") && !empty ($id))
         if ($db->Replace($tables['link']['name'], $data, 'ID', true) > 0)
            $tpl->assign('posted', true);
         else
            $tpl->assign('sql_error', $db->ErrorMsg());
   }
   else
      $tpl->assign('sql_error', $db->ErrorMsg());
}

$path = get_path($_SESSION['cid']);
$path[] = array ('ID' => '0', 'TITLE' => _L('Add Reciprocal Link for ' .$data['TITLE']), 'TITLE_URL' => '', 'DESCRIPTION' => _L('Rate A Link'));
$tpl->assign('path', $path);

$tpl->assign($data);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('add_reciprocal.tpl');
?>