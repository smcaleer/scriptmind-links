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
{
	$_SESSION['return'] = $_SERVER['HTTP_REFERER'];
}

$tpl->assign('ENABLE_REWRITE', ENABLE_REWRITE);

if ($_REQUEST['action'])
{
	list ($action, $id) = split (':', $_REQUEST['action']);
}

if ($_SESSION['is_admin'])
{
	$tpl->assign('admin_user', array (0 => _L('Editor'), 1 => _L('Administrator')));
}
else
{
	$tpl->assign('admin_user', array (0 => _L('Editor')));
}

$tpl->assign('yes_no', array (0 => _L('No'), 1 => _L('Yes')));

switch ($action)
{
	case 'C' :
		if (isset ($_SESSION['return']))
      {
			@ header ("Location: ".$_SESSION['return']);
			@ exit ();
		}
		break;
	case 'A' :
		if ($db->Execute("UPDATE `{$tables['user']['name']}` SET `STATUS` = '2' WHERE `ID` = ".$db->qstr($id)))
      {
			if (isset ($_SESSION['return']))
         {
            @ header ("Location: ".$_SESSION['return']);
            @ exit ();
			}
		}
      else
      {
			$tpl->assign('sql_error', $db->ErrorMsg());
		}
		break;
	case 'D' :
		if ($db->Execute("DELETE FROM `{$tables['user']['name']}` WHERE `ID` = ".$db->qstr($id)))
      {
			if ($db->Execute("DELETE FROM `{$tables['user_permission']['name']}` WHERE `USER_ID` = ".$db->qstr($id)))
         {
				if (isset ($_SESSION['return']))
            {
					@ header ("Location: ".$_SESSION['return']);
               @ exit ();
				}
			}
         else
         {
				$tpl->assign('sql_error', $db->ErrorMsg());
			}
		}
      else
      {
			$tpl->assign('sql_error', $db->ErrorMsg());
		}
		break;
	case 'E' :
		if (empty ($_REQUEST['submit']))
      {
			$data = $db->GetRow("SELECT * FROM `{$tables['user']['name']}` WHERE `ID` = ".$db->qstr($id));
		}
	case 'N' :
	default :
		if (empty ($_REQUEST['submit']))
      {
			if ($action == 'N')
         {
				$data = array ();
			}

			SmartyValidate :: connect($tpl);
			SmartyValidate :: register_form('conf_users_edit', true);
			SmartyValidate :: register_criteria('isValueUnique', 'validate_unique', 'conf_users_edit');

         SmartyValidate :: register_validator('v_LOGIN'      , 'LOGIN:4:25', 'isLength', false, false, 'trim', 'conf_users_edit');
         SmartyValidate :: register_validator('v_LOGIN_U'    , 'LOGIN:user:'.$id, 'isValueUnique', false, false, false, 'conf_users_edit');
         SmartyValidate :: register_validator('v_NAME'       , 'NAME:4:25' , 'isLength', false, false, 'trim', 'conf_users_edit');
         SmartyValidate :: register_validator('v_PASSWORD'   , 'PASSWORD:4:25', 'isLength', true, false, 'trim', 'conf_users_edit');
         SmartyValidate :: register_validator('v_PASSWORDC'  , 'PASSWORD:PASSWORDC', 'isEqual' , true , false, 'trim', 'conf_users_edit');
         SmartyValidate :: register_validator('v_EMAIL'      , 'EMAIL'             , 'isEmail' , false, false, 'trim', 'conf_users_edit');
         SmartyValidate :: register_validator('v_EMAIL_U'    , 'EMAIL:user:'.$id   , 'isValueUnique', false, false, 'trim', 'conf_users_edit');
		}
      else
      {
			SmartyValidate :: connect($tpl);
			$data = get_table_data('user');

         if (!isset($data['SUBMIT_NOTIF']))
            $data['SUBMIT_NOTIF'] = 0;

         if (!isset($data['PAYMENT_NOTIF']))
            $data['PAYMENT_NOTIF'] = 0;

         $data['PASSWORDC'] = $_REQUEST['PASSWORDC'];

			if (SmartyValidate :: is_valid($data, 'conf_users_edit'))
         {
				unset($data['PASSWORDC']);

            if (empty ($id))
               $id = $db->GenID($tables['user']['name'].'_SEQ');

				$data['ID'] = $id;

            if ($action == 'E')
            {
               if (empty($data['PASSWORD']))
                  $data['PASSWORD'] = $db->GetOne("SELECT `PASSWORD` FROM `{$tables['user']['name']}` WHERE `ID` = ".$db->qstr($id));
               else
                  $data['PASSWORD'] = encrypt_password($data['PASSWORD']);
            }
            else
               $data['PASSWORD'] = encrypt_password($data['PASSWORD']);

            if ($db->Replace($tables['user']['name'], $data, 'ID', true) > 0)
            {
               $tpl->assign('posted', true);

               if ($data['ADMIN'] != 1)
               {
                  @ header('Location: conf_user_permissions.php?action=N:0&u='.$id);
                  @ exit ();
               }
               else
               {
                  if ($action == 'N')
                     $data = array ();
                  else
                  {
                     if (isset ($_SESSION['return']))
                     {
                        @ header ('Location: '.$_SESSION['return']);
                        @ exit ();
                     }
                  }
               }
            }
            else
            {
               $tpl->assign('sql_error', $db->ErrorMsg());
            }
         }
      }
      break;
}

$tpl->assign($data);

$content = $tpl->fetch('admin/conf_users_edit.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('admin/main.tpl');
?>