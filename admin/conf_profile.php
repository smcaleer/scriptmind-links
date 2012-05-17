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

if (empty ($_REQUEST['submit']))
{
	$sql = "SELECT * FROM `{$tables['user']['name']}` WHERE `ID` = ".$db->qstr($_SESSION['user_id']);
	$row = $db->GetRow($sql);
	$tpl->assign($row);

	SmartyValidate :: connect($tpl);
	SmartyValidate :: register_form('conf_profile', true);

   SmartyValidate :: register_validator('v_LOGIN'      , 'LOGIN:4:25', 'isLength', false, false, 'trim', 'conf_profile');
   SmartyValidate :: register_validator('v_NAME'       , 'NAME:4:25' , 'isLength', false, false, 'trim', 'conf_profile');
   SmartyValidate :: register_validator('v_PASSWORD'   , 'PASSWORD:4:25', 'isLength', true, false, 'trim', 'conf_profile');
   SmartyValidate :: register_validator('v_PASSWORDC'  , 'PASSWORD:PASSWORDC', 'isEqual' , true , false, 'trim', 'conf_profile');
   SmartyValidate :: register_validator('v_EMAIL'      , 'EMAIL'             , 'isEmail' , false, false, 'trim', 'conf_profile');
}
else
{
   SmartyValidate :: connect($tpl);
   $data = get_table_data('user');

   $error = 0;

   if (!isset ($data['SUBMIT_NOTIF']))
      $data['SUBMIT_NOTIF'] = 0;

   if (!isset ($data['PAYMENT_NOTIF']))
      $data['PAYMENT_NOTIF'] = 0;

   $data['ID']        = $_SESSION['user_id'];
   $data['PASSWORDC'] = $_REQUEST['PASSWORDC'];

   if (SmartyValidate :: is_valid($data, 'conf_profile'))
   {
      unset ($data['PASSWORDC']);

      if (empty ($data['PASSWORD']))
         $data['PASSWORD'] = $db->GetOne("SELECT `PASSWORD` FROM `{$tables['user']['name']}` WHERE `ID` = ".$db->qstr($data['ID']));
      else
         $data['PASSWORD'] = encrypt_password($data['PASSWORD']);

      $mode = "UPDATE";
      $where = " `ID` = ".$db->qstr($data['ID']);
      if (!DEMO && $db->AutoExecute($tables['user']['name'], $data, $mode, $where) > 0)
      {
         $tpl->assign('posted', true);
      }
   }
}

$tpl->assign($data);

$content = $tpl->fetch('admin/conf_profile.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('admin/main.tpl');
?>