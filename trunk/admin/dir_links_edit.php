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
	list ($action, $id, $val) = split (':', $_REQUEST['action']);
}
$tpl->assign('stats', array (0 => _L('Inactive'), 1 => _L('Pending'), 2 => _L('Active'),));
switch ($action)
{
	case 'S' : //Set Status
		$error = false;
		if(PAY_ENABLE && $val == 2)
      {
			$sql = "SELECT `ID`, `PAYED`, `STATUS`, `EXPIRY_DATE` FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($id);
			$data = $db->GetRow($sql);
			if ($data['PAYED'] > 0)
         {
				$sql = "SELECT `ID`, `QUANTITY`, `UM` FROM `{$tables['payment']['name']}` WHERE `ID` = ".$db->qstr($data['PAYED']);
				$pdata = $db->GetRow($sql);
				$exp_date = calculate_expiry_date(time(), $pdata['QUANTITY'], $pdata['UM']);
				if ($exp_date!=0 && $data['EXPIRY_DATE'] == '')
            {
					$data['EXPIRY_DATE'] = gmdate('Y-m-d H:i:s', $exp_date);
				}
			}
			$data['STATUS'] = 2;
			if (db_replace('link', $data, 'ID') > 0)
         {
				send_status_notifications($id);
			}
         else
         {
				$tpl->assign('sql_error', $db->ErrorMsg());
				$error = true;
			}
		}
      else
      {
			if ($db->Execute("UPDATE `{$tables['link']['name']}` SET `STATUS` = ".$db->qstr($val)." WHERE `ID` = ".$db->qstr($id)))
         {
				send_status_notifications($id);
			}
         else
         {
				$tpl->assign('sql_error', $db->ErrorMsg());
				$error = true;
			}
		}
		if (!$error && isset ($_SESSION['return']))
      {
         @ header ('Location: '.$_SESSION['return']);
         @ exit ();
		}
		break;
	case 'A' :
		if ($db->Execute("UPDATE `{$tables['link']['name']}` SET `STATUS` = '2' WHERE `ID` = ".$db->qstr($id)))
      {
			send_status_notifications($id);
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
		$data = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($id));
		if ($db->Execute("DELETE FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($id)))
      {
			$data['STATUS'] = 0;
			send_status_notifications($data, false);
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
		break;
	case 'E' :
		if (empty ($_REQUEST['submit']))
      {
			$data = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($id));
		}
	case 'N' :
	default :
		if ($action == 'N') {
			$data['STATUS'] = 2;
			$data['RECPR_REQUIRED'] = REQUIRE_RECIPROCAL;
			if (FTR_ENABLE == 1)
         {
            $data['FEATURED'] = (isset ($_REQUEST['f']) && $_REQUEST['f'] == 1 ? 1 : 0);
			}
		}
		$categs = get_categs_tree($db, 0);
		$tpl->assign('categs', $categs);
		if (empty ($_REQUEST['submit']))
      {
			SmartyValidate :: connect($tpl);
			SmartyValidate :: register_form('dir_links_edit', true);
         SmartyValidate :: register_criteria('isValueUnique' , 'validate_unique'   , 'dir_links_edit');
         SmartyValidate :: register_criteria('isNotEqual'    , 'validate_not_equal', 'dir_links_edit');

         SmartyValidate :: register_validator('v_TITLE', 'TITLE', 'notEmpty', false, false, 'trim', 'dir_links_edit');
         SmartyValidate :: register_validator('v_TITLE_U', "TITLE:link:{$id}:CATEGORY_ID", 'isValueUnique', false, false, null, 'dir_links_edit');
         SmartyValidate :: register_validator('v_URL', 'URL', 'isURL', false, false, 'trim', 'dir_links_edit');
         SmartyValidate :: register_validator('v_URL_U', 'URL:link:'.$id.(ALLOW_MULTIPLE ? ':CATEGORY_ID' : ''), 'isValueUnique', false, false, null, 'dir_links_edit');
         SmartyValidate :: register_validator('v_CATEGORY_ID', 'CATEGORY_ID:0', 'isNotEqual', true, false,  null , 'dir_links_edit');
         SmartyValidate :: register_validator('v_RECPR_URL', 'RECPR_URL', 'isURL', true , false, 'trim', 'dir_links_edit');
         SmartyValidate :: register_validator('v_OWNER_NAME', 'OWNER_NAME', 'notEmpty', true , false, 'trim', 'dir_links_edit');
         SmartyValidate :: register_validator('v_OWNER_EMAIL', 'OWNER_EMAIL', 'isEmail', true , false, 'trim', 'dir_links_edit');
         SmartyValidate :: register_validator('v_EXPIRY_DATE', 'EXPIRY_DATE', 'isDate', true , false, 'trim', 'dir_links_edit');
		 
		 // Deeplink URL Validation
	     for($dl=1; $dl<=5; $dl++)
	     SmartyValidate :: register_validator('v_DEEPLINK_URL' . $dl, 'URL' . $dl, 'isURL' , true, false, 'trim', 'dir_links_edit');
		}
      else
      {
			SmartyValidate :: connect($tpl);
			$data = get_table_data('link');

			if ($action == 'N')
         {
				$data['IPADDRESS']     = get_client_ip();
				$data['VALID']         = 1;
				$data['RECPR_VALID']   = 1;
				$data['DATE_ADDED']    = gmdate('Y-m-d H:i:s');
				$data['DATE_MODIFIED'] = gmdate('Y-m-d H:i:s');
			}
			if (FTR_ENABLE == 1)
         {
				$data['FEATURED'] = ($_POST['FEATURED'] == '1' ? '1' : '0');
			}

         $data['NOFOLLOW']         = ($_POST['NOFOLLOW']       == '1' ? '1' : '0');
         $data['RECPR_REQUIRED']   = ($_POST['RECPR_REQUIRED'] == '1' ? '1' : '0');

			if (strlen (trim ($data['URL'])) > 0 && !preg_match ('#^http[s]?:\/\/#i', $data['URL']))
            $data['URL'] = "http://".$data['URL'];

         if (strlen (trim ($data['RECPR_URL'])) > 0 && !preg_match ('#^http[s]?:\/\/#i', $data['RECPR_URL']))
            $data['RECPR_URL'] = "http://".$data['RECPR_URL'];

         if (trim ($data['EXPIRY_DATE']) == '')
            $data['EXPIRY_DATE'] = '';
         else
         {
            if (strtotime ($data['EXPIRY_DATE']) != -1)
               $data['EXPIRY_DATE'] = date ('Y-m-d H:i:s', (strtotime ($data['EXPIRY_DATE'])));
         }

			if (SmartyValidate :: is_valid($data, 'dir_links_edit'))
         {
				if (empty ($id))
            {
					$id = $db->GenID($tables['link']['name'].'_SEQ');
				}

            if ($data['FEATURED'] == '1')
            {
               $AllowedFeat = check_allowed_feat($data['CATEGORY_ID']);
               $tpl->assign('AllowedFeat', $AllowedFeat);
            }

				if (ENABLE_PAGERANK)
            {
					require_once 'include/pagerank.php';

					$data['PAGERANK'] = get_page_rank($data['URL']);
					if (!empty($data['RECPR_URL']))
               {
						$data['RECPR_PAGERANK'] = get_page_rank($data['RECPR_URL']);
					}
				}
				$data['ID'] = $id;

            if (!isset ($data['RECPR_REQUIRED']))
               $data['RECPR_REQUIRED'] = 0;

				if (db_replace('link', $data, 'ID') > 0)
            {
					$tpl->assign('posted', true);
					if ($action == 'N')
               {
						$cid = $data['CATEGORY_ID'];
						$data = array ();
						$data['STATUS'] = 2;
						$data['CATEGORY_ID'] = $cid;
					}
               else
               {
                  send_status_notifications($id);
						if (isset ($_SESSION['return']))
                  {
							@ header('Location: '.$_SESSION['return']);
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
		$tpl->assign($data);
		$content = $tpl->fetch('admin/dir_links_edit.tpl');
		break;
}
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('admin/main.tpl');
?>