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
$tpl->assign('stats', array (0 => _L('Inactive'), 1 => _L('Pending'), 2 => _L('Active'),));

$tpl->assign('symbolic', $_REQUEST['s']=='1');

switch ($action)
{
	case 'C' :
		if (isset ($_SESSION['return']))
      {
         @ header ('Location: '.$_SESSION['return']);
         @ exit ();
		}
		break;
	case 'A' :
		if ($db->Execute("UPDATE `{$tables['category']['name']}` SET `STATUS` = '2' WHERE `ID` = ".$db->qstr($id)))
      {
			if (isset ($_SESSION['return']))
         {
            @ header ('Location: '.$_SESSION['return']);
            @ exit ();
			}
		}
      else
      {
			$tpl->assign('sql_error', $db->ErrorMsg());
		}
		break;
	case 'D' :
		$count_links = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `CATEGORY_ID` = ".$db->qstr($id));
		$count_categs = $db->GetOne("SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = ".$db->qstr($id));
		if ($count_links != 0 || $count_categs != 0)
      {
			$DO = (isset ($_REQUEST['DO']) ? $_REQUEST['DO'] : 'M');
			$error = false;
			if (isset ($_REQUEST['DO']))
         {
				if ($DO == 'D')
            {
					$db->Execute("DELETE FROM `{$tables['link']['name']}` WHERE `CATEGORY_ID` = ".$db->qstr($id));
					$db->Execute("DELETE FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = ".$db->qstr($id));
				}
            else
            {
					if ($_REQUEST['CATEGORY_ID'] != 0 && $_REQUEST['CATEGORY_ID'] != $id) {
						$path = get_path($_REQUEST['CATEGORY_ID']);
						foreach ($path as $item)
                  {
							if ($item['ID'] == $id)
                     {
								$error = true;
								break;
							}
						}
						if (!$error) {
							$db->Execute("UPDATE `{$tables['link']['name']}` SET `CATEGORY_ID` = ".$db->qstr($_REQUEST['CATEGORY_ID'])." WHERE `CATEGORY_ID` = ".$db->qstr($id));
							$db->Execute("UPDATE `{$tables['category']['name']}` SET `PARENT_ID` = ".$db->qstr($_REQUEST['CATEGORY_ID'])." WHERE `PARENT_ID` = ".$db->qstr($id));
						}
					}
               else
               {
						$error = true;
					}
				}
				if (!$error)
            {
					$count_links = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `CATEGORY_ID` = ".$db->qstr($id));
					$count_categs = $db->GetOne("SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = ".$db->qstr($id));
				}
			}
			if (empty ($_REQUEST['DO']) || $error)
         {
				$categs = get_categs_tree(0);
				$tpl->assign('categs', $categs);
				$tpl->assign('id', $id);
				$tpl->assign('error', $error);
				$tpl->assign('DO', $DO);
				$tpl->assign('count_links', $count_links);
				$tpl->assign('count_categs', $count_categs);
			}
		}
		if ($count_links == 0 && $count_categs == 0)
      {
			if ($db->Execute("DELETE FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($id)))
         {
				if (isset ($_SESSION['return']))
            {
					@ header ('Location: '.$_SESSION['return']);
					@ exit ();
				}
			}
         else
         {
				$tpl->assign('sql_error', $db->ErrorMsg());
			}
		}
		break;
	case 'E' :
		if (empty ($_REQUEST['submit']))
      {
			$data = $db->GetRow("SELECT * FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($id));
		}
	case 'N' :
	default :
		$categs = get_categs_tree(0);
		$tpl->assign('categs', $categs);
		if (empty ($_REQUEST['submit']))
      {
			if ($action == 'N')
         {
				$data = array ();
				$data['STATUS'] = 2;
			}
			SmartyValidate :: connect($tpl);
			SmartyValidate :: register_form('dir_categs_edit', true);
			if ($_REQUEST['s']!='1')
         {
				SmartyValidate :: register_criteria('isValueUnique', 'validate_unique', 'dir_categs_edit');
				SmartyValidate :: register_validator('v_TITLE', 'TITLE', 'notEmpty', false, false, 'trim', 'dir_categs_edit');
				SmartyValidate :: register_validator('v_TITLE_U', 'TITLE:category:'.$id.':PARENT_ID', 'isValueUnique', false, false, null, 'dir_categs_edit');
				if (ENABLE_REWRITE)
            {
					SmartyValidate :: register_validator('v_TITLE_URL', 'TITLE_URL:!^[\w_-]+$!', 'isRegExp', false, false, 'trim', 'dir_categs_edit');
					SmartyValidate :: register_validator('v_TITLE_URL_U', 'TITLE_URL:category:'.$id.':PARENT_ID', 'isValueUnique', false, false, null, 'dir_categs_edit');
				}
			}
         else
         {
				SmartyValidate :: register_criteria('isNotEqual', 'validate_not_equal', 'dir_categs_edit');
				SmartyValidate :: register_criteria('isNotEqualVariable', 'validate_not_equal_var', 'dir_categs_edit');
				SmartyValidate :: register_criteria('isSymbolicUnique', 'validate_symbolic_unique', 'dir_categs_edit');
				SmartyValidate :: register_criteria('isParentValid', 'validate_symbolic_parent', 'dir_categs_edit');

				SmartyValidate :: register_validator('v_SYMBOLIC_ID', 'SYMBOLIC_ID:0', 'isNotEqual', true, false, null, 'dir_categs_edit');
				SmartyValidate :: register_validator('v_SYMBOLIC_ID_E', 'SYMBOLIC_ID:PARENT_ID', 'isNotEqualVariable', true, false, null, 'dir_categs_edit');
				SmartyValidate :: register_validator('v_SYMBOLIC_ID_U', 'SYMBOLIC_ID', 'isSymbolicUnique', false, false, null, 'dir_categs_edit');
				SmartyValidate :: register_validator('v_SYMBOLIC_ID_P', 'SYMBOLIC_ID', 'isParentValid', false, false, null, 'dir_categs_edit');
			}
		}
      else
      {
			SmartyValidate :: connect($tpl);
			$data = get_table_data('category');

			$data['SYMBOLIC'] = (isset ($_REQUEST['s']) && $_REQUEST['s'] == 1 ? 1 : 0);

			if (strlen (trim ($data['TITLE_URL'])) == 0)
				$data['TITLE_URL'] = preg_replace('`[^\w_-]`', '_', $data['TITLE']);

			if (SmartyValidate :: is_valid($data, 'dir_categs_edit'))
         {
				if ($action == 'N')
					$data['DATE_ADDED'] = gmdate ('Y-m-d H:i:s');

				if (empty ($id))
					$id = $db->GenID($tables['category']['name'].'_SEQ');

				$data['ID'] = $id;
				if ($db->Replace($tables['category']['name'], $data, 'ID', true) > 0)
            {
					// Refresh editor permissions
					if (!$_SESSION['is_admin'])
               {
						$user_permission = "";
						$user_grant_permission = "";
						$user_permission_array = array ();
						$user_grant_permission_array = array ();
						get_editor_permission($_SESSION['user_id']);
						$_SESSION['user_permission'] = $user_permission;
						$_SESSION['user_grant_permission'] = $user_grant_permission;
						$_SESSION['user_permission_array'] = $user_permission_array;
						$_SESSION['user_grant_permission_array'] = $user_grant_permission_array;
					}

					$tpl->assign('posted', true);
					if ($action == 'N')
               {
                  $oldStatus = $data['STATUS'];
                  $data = array ();
                  $data['STATUS'] = $oldStatus;
                  unset ($oldStatus);
               }
               else
               {
						if (isset ($_SESSION['return']))
                  {
							@ header ('Location: '.$_SESSION['return']);
							@ exit ();
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

$content = $tpl->fetch('admin/dir_categs_edit.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('admin/main.tpl');
?>