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
# Load reCaptcha
require_once 'libs/recaptcha/recaptchalib.php';  


//Make an additional check if client is allowed to post/submit
//[Spam] protection
require_once 'include/check_post_rules.php';
$post_rules_unauthorized = check_post_rules($_POST);

//Evaluate payment options
if (PAY_ENABLE == '1' && PAYPAL_ACCOUNT != '')
{
	$price = array ();
	if (FTR_ENABLE == '1' && PAY_FEATURED > 0)
   {
		$price['featured'] = PAY_FEATURED;
	}
	if (PAY_NORMAL > 0)
   {
		$price['normal'] = PAY_NORMAL;
		if (PAY_ENABLE_FREE)
      {
			$price['free'] = 0;
		}
	}
	
   if (FTR_ENABLE == 1 && PAY_FEATURED_PLUS > 0)
      $price['featured_plus'] = PAY_FEATURED_PLUS;
      
   if (PAY_NORMAL_PLUS > 0)
      $price['normal_plus'] = PAY_NORMAL_PLUS;
      
	if (PAY_RECPR > 0)
   {
		$price['reciprocal'] = PAY_RECPR;
	}
	$tpl->assign('price', $price);

	if (isset ($_REQUEST['LINK_TYPE']))
   {
		$link_type = $_REQUEST['LINK_TYPE'];
		switch ($link_type)
      {
			case 'reciprocal' :
				$recpr_required = 1;
				break;
			case 'free' :
				$recpr_required = REQUIRE_RECIPROCAL;
				break;
			default :
				$recpr_required = 0;
				break;
		}
	}
   else
   {
		$recpr_required = 0;
	}
	$_SESSION['SmartyValidate']['submit_link']['validators'][6]['empty'] = ($recpr_required ? 0 : 1);
	$_SESSION['SmartyValidate']['submit_link']['validators'][7]['empty'] = ($recpr_required ? 0 : 1);
}
else
{
	$recpr_required = REQUIRE_RECIPROCAL;
}
$tpl->assign('recpr_required', $recpr_required);

//Determine category
$CategoryID = (!empty($_REQUEST['c']) && preg_match ('`^[\d]+$`', $_REQUEST['c']) ? intval ($_REQUEST['c']) :
               (!empty ($_SERVER['HTTP_REFERER']) ? get_category($_SERVER['HTTP_REFERER']) : 0));
$CategoryID = ($CategoryID > 0 ? $CategoryID : 0); //Make sure the category ID is valid

if (empty ($_REQUEST['submit']))
{
	if (!empty ($_SERVER['HTTP_REFERER']))
		$_SESSION['return'] = $_SERVER['HTTP_REFERER'];

	$data = array ();
   $data['CATEGORY_ID'] = $CategoryID;
	$data['RECPR_REQUIRED'] = $recpr_required;

   SmartyValidate :: disconnect();
	SmartyValidate :: connect($tpl);
	SmartyValidate :: register_form('submit_link', true);

	SmartyValidate :: register_criteria('isValueUnique' , 'validate_unique'    , 'submit_link');
   SmartyValidate :: register_criteria('isUrlUnique'   , 'validateUrlUnique'  , 'submit_link');
	SmartyValidate :: register_criteria('isNotEqual'    , 'validate_not_equal' , 'submit_link');
	SmartyValidate :: register_criteria('isURLOnline'   , 'validate_url_online', 'submit_link');
   SmartyValidate :: register_criteria('isRecprOnline' , 'validate_recpr_link', 'submit_link');
   SmartyValidate :: register_criteria('isCaptchaValid', 'validate_captcha'   , 'submit_link');

   SmartyValidate :: register_validator('v_TITLE'         , 'TITLE', 'notEmpty'  , false, false, 'trim', 'submit_link');
   SmartyValidate :: register_validator('v_TITLE_U'       , 'TITLE:link::CATEGORY_ID'.$EditUnique, 'isValueUnique', false, false, null, 'submit_link');

   SmartyValidate :: register_validator('v_URL'           , 'URL', 'isURL'       , false, false, 'trim', 'submit_link');
   SmartyValidate :: register_validator('v_URL_ONLINE'    , 'URL', 'isURLOnline' , false, false,  null , 'submit_link');
   SmartyValidate :: register_validator('v_URL_U'         , 'URL:link'.(ALLOW_MULTIPLE ? '::CATEGORY_ID' : ':'), 'isUrlUnique', false, false, null, 'submit_link');

   SmartyValidate :: register_validator('v_CATEGORY_ID'   , 'CATEGORY_ID:0'      , 'isNotEqual', false, false, null, 'submit_link');

   SmartyValidate :: register_validator('v_RECPR_URL'     , 'RECPR_URL'          , 'isURL'         , ($recpr_required ? false : true), false, 'trim', 'submit_link');
   SmartyValidate :: register_validator('v_RECPR_ONLINE'  , 'RECPR_URL'          , 'isURLOnline'   , ($recpr_required ? false : true), false, null, 'submit_link');
   SmartyValidate :: register_validator('v_RECPR_LINK'    , 'RECPR_URL'          , 'isRecprOnline' , ($recpr_required ? false : true), false, null, 'submit_link');

   SmartyValidate :: register_validator('v_OWNER_NAME' , 'OWNER_NAME'         , 'notEmpty'      , false, false, 'trim', 'submit_link');
   SmartyValidate :: register_validator('v_OWNER_EMAIL', 'OWNER_EMAIL'        , 'isEmail'       , false, false, 'trim', 'submit_link');

   if (count ($price) > 0)
      SmartyValidate :: register_validator('v_LINK_TYPE'  , 'LINK_TYPE'          , 'notEmpty'      , false, false, 'trim', 'submit_link');
   // Deeplink URL Validation
   for($dl=1; $dl<=5; $dl++)
   SmartyValidate :: register_validator('v_DEEPLINK_URL' . $dl, 'URL' . $dl, 'isURL' , true, false, 'trim', 'submit_link');
   
   
}
else
{
	SmartyValidate :: connect($tpl);
	$data = get_table_data('link');

	$data['STATUS']         = 1;
	$data['IPADDRESS']      = get_client_ip();
	$data['VALID']          = 1;
	$data['LINK_TYPE']      = $link_type;
	$data['RECPR_REQUIRED'] = $recpr_required;

	if ($recpr_required)
   {
		$data['RECPR_VALID'] = 1;
		$data['RECPR_LAST_CHECKED'] = gmdate ('Y-m-d H:i:s');
	}

	$data['LAST_CHECKED']  = gmdate ('Y-m-d H:i:s');
	$data['DATE_ADDED']    = gmdate ('Y-m-d H:i:s');
	$data['DATE_MODIFIED'] = gmdate ('Y-m-d H:i:s');
	$data['DESCRIPTION'] = strip_tags($data['DESCRIPTION']);
	$data['TITLE'] = strip_tags($data['TITLE']);
	$data['OWNER_NAME'] = strip_tags($data['OWNER_NAME']);
	
   if (strlen (trim ($data['URL'])) > 0 && !preg_match ('#^http[s]?:\/\/#i', $data['URL']))
      $data['URL'] = "http://".$data['URL'];

   if (strlen (trim ($data['RECPR_URL'])) > 0 && !preg_match ('#^http[s]?:\/\/#i', $data['RECPR_URL']))
      $data['RECPR_URL'] = "http://".$data['RECPR_URL'];

	/*if (VISUAL_CONFIRM == 1 && !empty ($_POST['CAPTCHA']))
      $data = array_merge ($data, array ('CAPTCHA' => $_POST['CAPTCHA']));*/

	$rc_resp = validateReCaptcha();
	if($rs_resp === true)
		$tpl->assign('reCaptchaError', 1);
	else
		$tpl->assign('reCaptchaError', $rc_resp);
	
	if (SmartyValidate :: is_valid($data, 'submit_link') && ($rc_resp === true))
   {
		

		if (ENABLE_PAGERANK)
      {
			require_once 'include/pagerank.php';
			$data['PAGERANK'] = get_page_rank($data['URL']);
			if (!empty ($data['RECPR_URL']))
         {
            $data['RECPR_PAGERANK'] = get_page_rank($data['RECPR_URL']);
			}
		}

		$id = $db->GenID($tables['link']['name'].'_SEQ');
		$data['ID'] = (!empty ($id) ? intval ($id) : '');

		$data['LINK_TYPE'] = $link_type_int[$link_type];
		switch ($link_type)
      {
			case 'free':
				$data['NOFOLLOW'] = 1;
				break;
			case 'featured': case 'featured_plus':
				$data['FEATURED'] = 1;
				break;
		}

      $data['OWNER_NOTIF'] = ($price[$link_type] > 0 ? 0 : 1 );
      $data['PAYED']       = ($price[$link_type] > 0 ? 0 : -1);

		if ($db->Replace($tables['link']['name'], $data, 'ID', true) > 0)
      {
			$tpl->assign('posted', true);
			send_submit_notifications($data);

			if ($price[$link_type] > 0)
         {
            //Move to payment page
				@ header("Location: payment.php?id=".$data['ID']);
				@ exit;
			}
         else
         {
				unset ($data);
			}
		}
      else
      {
			$tpl->assign('error', true);
         $tpl->assign('sqlError', $db->ErrorMsg());
		}
	}
}
unset ($_SESSION['CAPTCHA']);

function validateReCaptcha()
{
$reCaptchaResp = recaptcha_check_answer(RECAPTCHA_PRIVATE_KEY  ,$_SERVER['REMOTE_ADDR'] ,$_POST['recaptcha_challenge_field'],$_POST['recaptcha_response_field']);
if ($reCaptchaResp->is_valid)
  return true;
else
  return $reCaptchaResp->error;
}

$path = array ();
$path[] = array ('ID' => '0', 'TITLE' => _L(SITE_NAME), 'TITLE_URL' => DOC_ROOT, 'DESCRIPTION' => SITE_DESC);
$path[] = array ('ID' => '0', 'TITLE' => _L('Submit Link'), 'TITLE_URL' => '', 'DESCRIPTION' => _L('Submit a new link to the directory '));
$tpl->assign('path', $path);

$categs = get_categs_tree(0);
$tpl->assign('categs', $categs);
$tpl->assign($data);
$tpl->assign('LINK_TYPE', $link_type);

/* Top level Categories */
$topcats = $db->GetAll("SELECT * FROM `{$tables['category']['name']}` WHERE `STATUS` = 2 AND `PARENT_ID` = 0 ORDER BY `TITLE`");
$tpl->assign('topcats', $topcats);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('submit.tpl', $id);
?>