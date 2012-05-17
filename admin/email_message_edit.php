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

if (empty ($_REQUEST['submit']) && !empty ($_SERVER['HTTP_REFERER']))
   $_SESSION['return'] = $_SERVER['HTTP_REFERER'];
if ($_REQUEST['action'])
   list ($action, $id) = split(':', $_REQUEST['action']);

switch ($action)
{
   case 'D' :
      if ($db->Execute("DELETE FROM `{$tables['email_tpl']['name']}` WHERE `ID` = ".$db->qstr($id)))
      {
         if (isset ($_SESSION['return']))
         {
            @ header('Location: '.$_SESSION['return']);
            @ exit ();
         }
      }
      else
         $tpl->assign('sql_error', $db->ErrorMsg());
      break;
   case 'E' :
      if (empty ($_REQUEST['submit']))
         $data = $db->GetRow("SELECT * FROM `{$tables['email_tpl']['name']}` WHERE `ID` = ".$db->qstr($id));
   case 'N' :
   default :
      if ($id)
         $where = "WHERE `ID` != ".$db->qstr($id);

      if (empty ($_POST['submit']))
      {
         SmartyValidate :: disconnect();
         SmartyValidate :: connect($tpl, true);
         SmartyValidate :: register_criteria('isEmailAndAddLinkValid', 'validate_email_and_add_link');
         SmartyValidate :: register_validator('v_VALIDATE_EMAIL_TYPE', 'TPL_TYPE', 'isEmailAndAddLinkValid', false, false, 'trim');
         SmartyValidate :: register_validator('v_TITLE'              , 'TITLE'   , 'notEmpty', false, false, 'trim');
         SmartyValidate :: register_validator('v_SUBJECT'            , 'SUBJECT' , 'notEmpty', false, false, 'trim');
         SmartyValidate :: register_validator('v_BODY'               , 'BODY'    , 'notEmpty', false, false, 'trim');
      }
      else
      {
         SmartyValidate :: connect($tpl);
         $data = get_table_data('email_tpl');
         if (SmartyValidate :: is_valid($data))
         {
            if (empty ($id))
               $id = $db->GenID($tables['email_tpl']['name'].'_SEQ');

            $data['ID'] = $id;
            if ($db->Replace($tables['email_tpl']['name'], $data, 'ID', true) > 0)
            {
               $tpl->assign('posted', true);
               if (isset ($_SESSION['return']))
               {
                  @ header('Location: '.$_SESSION['return']);
                  @ exit ();
               }
            }
            else
            {
               $tpl->assign('sql_error', $db->ErrorMsg());
            }
         }
      }
      $tpl->assign('tpl_types', $email_tpl_types);
      $tpl->assign($data);
      $content = $tpl->fetch('admin/email_message_edit.tpl');
      break;
}
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('admin/main.tpl');
?>