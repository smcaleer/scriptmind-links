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

unset ($u);

if($_REQUEST['u'])
{
	$u = $_REQUEST['u'];
	$_SESSION[SCRIPT_NAME]['u'] = $u;
}
elseif ($_SESSION[SCRIPT_NAME]['u'])
{
	$u = $_SESSION[SCRIPT_NAME]['u'];
}

if ($u)
{
	$where = " WHERE USER_ID = ".$db->qstr($u);
}

$tpl->assign('ENABLE_REWRITE', ENABLE_REWRITE);
$tpl->assign('admin_user', array (0 => _L('Editor'), 1 => _L('Administrator')));
$tpl->assign('columns', array ('CATEGORY' => _L('Category'), 'CATEGORY_PATH' => _L('Category Path')));
$tpl->assign('user_detail_columns', array ('LOGIN' => _L('LOGIN'), 'NAME' => _L('NAME')));

if (defined('SORT_FIELD'))
{
	$orderBy = ' ORDER BY C.TITLE '.SORT_ORDER;
}

switch ($action) {
	case 'A' :
         delete_child_categories();
         $data['ID']          = $id;
         $data['USER_ID']     = $u;
         $data['CATEGORY_ID'] = $id;
         $id = $db->GenID($tables['user_permission']['name'].'_SEQ');
         if (db_replace('user_permission', $data, 'ID') > 0)
            $tpl->assign('posted', 'Permission granted.');
         else
            $tpl->assign('sql_error', $db->ErrorMsg());

         break;
   case 'C' :
         $tpl->assign('CATEGORY_ID', $id);
         break;
	case 'D' :
         if ($db->Execute("DELETE FROM `{$tables['user_permission']['name']}` WHERE `ID` = ".$db->qstr($id)))
         {
            $tpl->assign('posted', 'Permission removed.');
            break;
         }
         else
            $tpl->assign('sql_error', $db->ErrorMsg());

	case 'N' :
	default :
		if (empty ($_REQUEST['submit']))
      {
         SmartyValidate :: connect($tpl);
         SmartyValidate :: register_form('conf_user_permissions', true);
         SmartyValidate :: register_criteria('isValueUnique'   , 'validate_unique', 'conf_user_permissions');
         SmartyValidate :: register_criteria('isNotEqual'      , 'validate_not_equal', 'conf_user_permissions');
         SmartyValidate :: register_criteria('isNotSubCat'     , 'validate_not_sub_category'            , 'conf_user_permissions');
         SmartyValidate :: register_validator('v_CATEGORY_ID'  , 'CATEGORY_ID:0'                        , 'isNotEqual'   , true , false, null, 'conf_user_permissions');
         SmartyValidate :: register_validator('v_CATEGORY_ID_U', "CATEGORY_ID:user_permission:0:USER_ID", 'isValueUnique', false, false, null, 'conf_user_permissions');
         SmartyValidate :: register_validator('v_CATEGORY_ID_S', "CATEGORY_ID"                          , 'isNotSubCat'  , false, false, null, 'conf_user_permissions');
		}
      else {
			SmartyValidate :: connect($tpl);
			$data = get_table_data('user_permission');

			$data['USER_ID'] = $u;
			if (SmartyValidate :: is_valid($data, 'conf_user_permissions'))
         {
            // Check if category is parent to existing categories.
            $child_categories = find_child_categories();

            if ($child_categories > 0)
            {
               $tpl->assign('CHILD_CATEGORIES', $child_categories);
               $tpl->assign('WARN', true);
               $category = $db->GetOne("SELECT `TITLE` FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($data['CATEGORY_ID']));
               $tpl->assign('CATEGORY', $category);
            }
            else
            {
               $id = $db->GenID($tables['user_permission']['name'].'_SEQ');
               $data['ID'] = $id;
               if (db_replace('user_permission', $data, 'ID') > 0)
                  $tpl->assign('posted', 'Permission granted.');
               else
                  $tpl->assign('sql_error', $db->ErrorMsg());
            }
			}
			$tpl->assign('CATEGORY_ID', $data['CATEGORY_ID']);
		}
}

if ($_SESSION['is_admin'])
{
   $list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['user_permission']['name']}` {$where}");
   $page = get_page($list_total);
   $tpl->assign('list_limit', LINKS_PER_PAGE);
   $tpl->assign('list_total', $list_total);
   $sql = "SELECT U.*, ".$db->IfNull('C.TITLE', "'Top'")." AS CATEGORY FROM `{$tables['user_permission']['name']}` AS U LEFT OUTER JOIN `{$tables['category']['name']}` AS C ON U.CATEGORY_ID = C.ID {$where} {$orderBy}";
   $rs = $db->SelectLimit($sql , LINKS_PER_PAGE, LINKS_PER_PAGE * ($page - 1));
   $list = $rs->GetAssoc(true);
   // Go through each link to get category path
   foreach($list as $category => $category_row)
      $list[$category]['CATEGORY_PATH'] = get_path($list[$category]['CATEGORY_ID']);

   $tpl->assign('list', $list);
}
else
{
   $list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['user_permission']['name']}` {$where} AND (".str_replace("ID","CATEGORY_ID",$_SESSION['user_grant_permission']).")");
   $page = get_page($list_total);
   $tpl->assign('list_limit', LINKS_PER_PAGE);
   $tpl->assign('list_total', $list_total);
   $sql = "SELECT U.*, ".$db->IfNull('C.TITLE', "'Top'")." AS CATEGORY FROM {$tables['user_permission']['name']} AS U LEFT OUTER JOIN {$tables['category']['name']} AS C ON U.CATEGORY_ID = C.ID $where AND (".str_replace("ID","U.CATEGORY_ID",$_SESSION['user_grant_permission']).") {$orderBy}";
   $rs = $db->SelectLimit($sql , LINKS_PER_PAGE, LINKS_PER_PAGE * ($page - 1));
   $list = $rs->GetAssoc(true);
   // Go through each link to get category path
   foreach($list as $category => $category_row)
      $list[$category]['CATEGORY_PATH'] = get_path($list[$category]['CATEGORY_ID']);

   $tpl->assign('list', $list);
}

$categs = get_grant_categs_tree(0);
$tpl->assign('categs', $categs);

$user_detail = $db->GetRow("SELECT `LOGIN`, `NAME` FROM `{$tables['user']['name']}` WHERE `ID` = ".$db->qstr($u));
$tpl->assign('user_detail', $user_detail);

$content = $tpl->fetch('admin/conf_user_permissions.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('admin/main.tpl');
?>