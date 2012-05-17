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

require_once '../include/version.php';
require_once '../include/config.php';
require_once 'include/tables.php';
require_once 'include/functions.php';
require_once 'libs/intsmarty/intsmarty.class.php';
require_once 'libs/smarty/SmartyValidate.class.php';
require_once 'libs/adodb/adodb.inc.php';

session_start();

if (get_magic_quotes_gpc())
{
   function stripslashes_deep($value)
   {
       $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
       return $value;
   }

   $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
   $_COOKIE  = array_map('stripslashes_deep', $_COOKIE);
}

$db = ADONewConnection(DB_DRIVER);
if ($db->Connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME))
{
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   read_config($db);
}
else
{
	define('ERROR', 'ERROR_DB_CONNECT');
}

if (DEBUG===1)
{
	set_log('admin_log.txt');
}

define ('DOC_ROOT', substr ($_SERVER["SCRIPT_NAME"], 0, strrpos ($_SERVER["SCRIPT_NAME"], '/')));
$directory_root = preg_replace ('`[\/]?admin[\/]?$`', '', DOC_ROOT);
define ('DIRECTORY_ROOT', $directory_root);

$tpl = get_tpl();
$tpl->assign('date_format', '%D %H:%M:%S');

require_once 'include/constants.php';

$featured_where = "(FEATURED = 1 AND (EXPIRY_DATE > ".$db->DBTimeStamp(time())." OR EXPIRY_DATE IS NULL))";

if (empty ($_SESSION['user_id']))
{
	$f = $_SERVER['SCRIPT_NAME'];
	if (($p = strrpos ($f, '/')) !== false)
   {
		$f = substr ($f, $p + 1);
	}
	if ($f != 'login.php')
   {
		if (empty ($_SESSION['return']))
      {
			$_SESSION['return'] = request_uri();
		}
		@ header("Location: login.php");
		@ exit ();
	}
}

if ($_SESSION['is_admin']) {
	$menu = array (
      'index' => _L('Home'),
		'dir' => array (
					'label' => _L('Directory'),
					'menu'  => array(
						'categs' => _L('Categories'),
						'links' => _L('Links'),
						'ftr_links' => array('label' => _L('Featured Links'), 'url' => 'dir_links.php?f=1', 'disabled' => FTR_ENABLE!=='1'),
						'approve_categs' => _L('Approve Categories'),
						'approve_links' => _L('Approve Links'),
						'validate' => _L('Validate Links'),
						)
					),
		'email' => array(
					'label' => _L('Emailer'),
					'menu'  => array(
						'send' => _L('Send Email'),
						'send_and_add_link' => _L('Send Email and Add Link'),
						'sent_view' => _L('Sent Emails Report'),
						'import' => _L('Import Email Messages'),
						'export' => _L('Export Email Messages'),
						)
					),
		'conf' => array(
					'label' => _L('Settings'),
					'menu'  => array(
						array('label' => _L('Directory'), 'url' => 'conf_settings.php?c=1'),
						array('label' => _L('Link Submit'), 'url' => 'conf_settings.php?c=3'),
						array('label' => _L('Emailer'), 'url' => 'conf_settings.php?c=4'),
						array('label' => _L('Notifications'), 'url' => 'conf_settings.php?c=5'),
						array('label' => _L('Admin Area'), 'url' => 'conf_settings.php?c=6'),
						array('label' => _L('Featured Links'), 'url' => 'conf_settings.php?c=7'),
						array('label' => _L('Payment'), 'url' => 'conf_settings.php?c=9'),
						array('label' => _L('reCaptcha'), 'url' => 'conf_settings.php?c=10'),
						//array('label' => _L('Paypal Integration'), 'url' => 'conf_settings.php?c=8'),
						'message' => array('label' => _L('Edit Email Templates'), 'url' => 'email_message.php'),
						'payment' => array('label' => _L('Payments'), 'url' => 'conf_payment.php', 'disabled' => PAY_ENABLE!=='1'),
						'profile' => _L('Profile'),
						)
					),
		'users' => array(
					'label'=>_L('Users'),
					'menu' => array
					(
						'userlist' => array('label'=> _L('User List'), url=> 'conf_users.php'),
						'adduser' => array('label'=>_L('Add User'), url=> 'conf_users_edit.php?action=N'),
					)
				),
		'logout' => _L('Logout')
	);
}
else
{
	$menu = array(
      'index' => _L('Home'),
		'dir' => array(
					'label' => _L('Directory'),
					'menu'  => array(
						'categs' => _L('Categories'),
						'links' => _L('Links'),
						'approve_links' => _L('Approve Links'),
						)
					),
		'conf' => array(
					'label' => _L('System'),
					'menu'  => array(
						'profile' => _L('Profile'),
						)
					),
		'logout' => _L('Logout')
	);
}

$conf_categs = array(
 		'1' => _L('Site'),
 		'2' => _L('Directory'),
 		'3' => _L('Link Submit'),
 		'4' => _L('Emailer'),
 		'5' => _L('Notifications'),
 		'6' => _L('Admin Area'),
 		'7' => _L('Featured Links'),
 		//'8' => _L('Paypal Integration'),
 		'8' => _L('Payment'),
		'10' => _L('reCaptcha'),
);
$tpl->assign('menu', $menu);

$f = $_SERVER['SCRIPT_NAME'];
if (($ptmp = strrpos ($f, '/')) !== false)
{
	$f = substr ($f, $ptmp + 1);
}
$current_script = $f;
if (($ptmp = strrpos ($f, '.')) !== false)
{
	$f = substr ($f, 0, $ptmp);
}

define('SCRIPT_NAME', $f);
$ptmp = explode ('_', $f, 2);
if (count ($ptmp) > 1)
{
	$t = $menu[$p[0]]['menu'][$ptmp[1]];
}
else
{
	$t = $menu[$ptmp[0]];
}

if ($t != 'Home')
   $tpl->assign('title', $t);

if ($_REQUEST['r'])
{
	unset ($_SESSION[SCRIPT_NAME]);
}

if ($_REQUEST['sort'])
{
	if ($_SESSION['sort'][SCRIPT_NAME]['field'] == $_REQUEST['sort'])
   {
		$_SESSION['sort'][SCRIPT_NAME]['order'] = ($_SESSION['sort'][SCRIPT_NAME]['order'] == 'ASC' ? 'DESC' : 'ASC');
	}
   else
   {
		$_SESSION['sort'][SCRIPT_NAME]['field'] = $_REQUEST['sort'];
		$_SESSION['sort'][SCRIPT_NAME]['order'] = 'ASC';
	}
	@ header ('Location: '.SCRIPT_NAME.'.php');
	@ exit;
}
if ($_SESSION['sort'][SCRIPT_NAME])
{
	define ('SORT_FIELD', $_SESSION['sort'][SCRIPT_NAME]['field']);
	define ('SORT_ORDER', $_SESSION['sort'][SCRIPT_NAME]['order']);
	$tpl->assign('SORT_FIELD', SORT_FIELD);
	$tpl->assign('SORT_ORDER', SORT_ORDER);
}

// Disallow access to the page if it's not allowed for editors
if (!$_SESSION['is_admin'])
{
	if ($current_script != "login.php" && $current_script != "index.php" && $current_script != "dir_categs.php" && $current_script != "dir_categs_edit.php" && $current_script != "dir_links.php" && $current_script != "dir_links_edit.php" && $current_script != "dir_approve_links.php" && $current_script != "unauthorised.php" && $current_script != "conf_profile.php")
   {
		@ header ('Location: unauthorised.php');
		@ exit ();
	}
}

$conf = array(
		#Site
 		array('ID' => 'SITE_NAME',
			  'NAME' => _L('Name'),
			  'DESCRIPTION' => _L('Your site name.'),
			  'CONFIG_GROUP' => '1',
			  'TYPE' => 'STR',
			  'REQUIRED' => '1'),
 		array('ID' => 'SITE_URL',
			  'NAME' => _L('URL'),
			  'DESCRIPTION' => _L('Your site URL.'),
			  'CONFIG_GROUP' => '1',
			  'TYPE' => 'URL',
			  'REQUIRED' => '1'),
 		array('ID' => 'SITE_DESC',
			  'NAME' => _L('Description'),
			  'DESCRIPTION' => _L('Your site description.'),
			  'CONFIG_GROUP' => '1',
			  'TYPE' => 'STR',
			  'REQUIRED' => '1'),

		#Directory
 		array('ID' => 'DIRECTORY_TITLE',
			  'NAME' => _L('Directory title'),
			  'DESCRIPTION' => _L('Page title to be used on the directory pages'),
			  'CONFIG_GROUP' => '1',
			  'TYPE' => 'STR',
			 'REQUIRED' => '1'),
 		array('ID' => 'ENABLE_REWRITE',
			  'NAME' => _L('Enable URL rewrite'),
			  'DESCRIPTION' => _L('Enables generation of search engine friendly and descriptive URLs.'),
			  'CONFIG_GROUP' => '1',
			  'TYPE' => 'LOG',
			  'REQUIRED' => '1'),
 		array('ID' => 'CATS_PER_ROW',
			  'NAME' => _L('Categories columns'),
			  'DESCRIPTION' => _L('Number of columns.'),
			  'CONFIG_GROUP' => '1',
			  'TYPE' => 'INT',
			  'REQUIRED' => '1'),
 		array('ID' => 'CATS_COUNT',
			  'NAME' => _L('Show count'),
			  'DESCRIPTION' => _L('Show the number of links and subcategories next to the category name'),
			  'CONFIG_GROUP' => '1',
			  'TYPE' => 'LOG',
			  'REQUIRED' => '1'),
 		array('ID' => 'CATS_PREVIEW',
			  'NAME' => _L('Subcategories preview'),
			  'DESCRIPTION' => _L('No. of subcategories shown on the main page for each category (0 to disable)'),
			  'CONFIG_GROUP' => '1',
			  'TYPE' => 'INT',
			  'REQUIRED' => '1'),
 		array('ID' => 'SHOW_PAGERANK',
			  'NAME' => _L('Show PageRank'),
			  'DESCRIPTION' => _L('Show the Google PageRank for links.'),
			  'CONFIG_GROUP' => '1',
			  'TYPE' => 'LOG',
			  'REQUIRED' => '1'),
 		array('ID' => 'DEFAULT_SORT',
			  'NAME' => _L('Default link sorting'),
			  'DESCRIPTION' => _L('Default sorting for links on directory pages.'),
			  'CONFIG_GROUP' => '1',
			  'TYPE' => 'LKP',
			  'OPTIONS' => array('P' => _L('PageRank'), 'H' => _L('Hits'), 'A' => _L('Alphabetical')),
			  'REQUIRED' => '1'),
 		array('ID' => 'RECPR_NOFOLLOW',
			  'NAME' => _L('No follow on broken recipr.'),
			  'DESCRIPTION' => _L('Set nofollow attribute on links with broken/missing reciprocal link.'),
			  'CONFIG_GROUP' => '1',
			  'TYPE' => 'LKP',
			  'OPTIONS' => array('0' => _L('Disabled'), '1' => _L('If recpr. required'), '2' => _L('All')),
			  'REQUIRED' => '1'),
 		array('ID' => 'LINKS_TOP',
			  'NAME' => _L('Links on top pages'),
			  'DESCRIPTION' => _L('Number of links on the top pages (Latest Links, Top Hits).'),
			  'CONFIG_GROUP' => '1',
			  'TYPE' => 'INT',
			  'REQUIRED' => '1'),
 		array('ID' => 'ENABLE_RSS',
			  'NAME' => _L('Enable RSS'),
			  'DESCRIPTION' => _L('Enable RSS feeds on directory pages'),
			  'CONFIG_GROUP' => '1',
			  'TYPE' => 'LOG',
			  'REQUIRED' => '1'),
		# Pager Mod
 		array('ID' => 'PAGER_LPP',
			  'NAME' => _L('Paging: Maximum per page.'),
			  'DESCRIPTION' => _L('Number of links on the links pages.'),
			  'CONFIG_GROUP' => '1',
			  'TYPE' => 'INT',
			  'REQUIRED' => '1'),
		# Blank Window option
 		array('ID' => 'ENABLE_BLANK',
			  'NAME' => _L('New Windows'),
			  'DESCRIPTION' => _L('Enable links to open in a new window.'),
			  'CONFIG_GROUP' => '1',
			  'TYPE' => 'LOG',
			  'REQUIRED' => '1'),

		#Link Submit
 		array('ID' => 'VISUAL_CONFIRM',
			  'NAME' => _L('Visual confirmation'),
			  'DESCRIPTION' => _L('Display antispam visual confirmation code on link submit page.'),
			  'CONFIG_GROUP' => '3',
			  'TYPE' => 'LOG',
			  'REQUIRED' => '1'),
 		array('ID' => 'REQUIRE_RECIPROCAL',
			  'NAME' => _L('Require reciprocal link'),
			  'DESCRIPTION' => _L('Require reciprocal link on link submit page.'),
			  'CONFIG_GROUP' => '3',
			  'TYPE' => 'LOG',
			  'REQUIRED' => '1'),
 		array('ID' => 'ALLOW_MULTIPLE',
			  'NAME' => _L('Allow multiple submit'),
			  'DESCRIPTION' => _L('Allows the same link to be submited in different categories.'),
			  'CONFIG_GROUP' => '3',
			  'TYPE' => 'LOG',
			  'REQUIRED' => '1'),

		#Emailer
 		array('ID' => 'EMAIL_METHOD',
			  'NAME' => _L('Emailer Method '),
			  'DESCRIPTION' => _L('Method used to send emails.'),
			  'CONFIG_GROUP' => '4',
			  'TYPE' => 'LKP',
			  'OPTIONS' => array('mail' => _L('PHP mail function'),'smtp' => _L('SMTP server'), 'sendmail' => _L('sendmail command')),
			  'REQUIRED' => '1'),
 		array('ID' => 'EMAIL_SERVER',
			  'NAME' => _L('Email server (SMTP only)'),
			  'DESCRIPTION' => _L('The email server used to send emails.'),
			  'CONFIG_GROUP' => '4',
			  'TYPE' => 'STR',
			  'REQUIRED' => '0'),
 		array('ID' => 'EMAIL_USER',
			  'NAME' => _L('SMTP user (SMTP only)'),
			  'DESCRIPTION' => _L('Only if your email server requires authentication.'),
			  'CONFIG_GROUP' => '4',
			  'TYPE' => 'STR',
			  'REQUIRED' => '0'),
 		array('ID' => 'EMAIL_PASS',
			  'NAME' => _L('SMTP password (SMTP only)'),
			  'DESCRIPTION' => _L('Only if your email server requires authentication.'),
			  'CONFIG_GROUP' => '4',
			  'TYPE' => 'PAS',
			  'REQUIRED' => '0'),
 		array('ID' => 'EMAIL_SENDMAIL',
			  'NAME' => _L('sendmail path (sendmail only)'),
			  'DESCRIPTION' => _L('Path to sendmail program'),
			  'CONFIG_GROUP' => '4',
			  'TYPE' => 'STR',
			  'REQUIRED' => '0'),

		#Notifications
 		array('ID' => 'NTF_SUBMIT_TPL',
			  'NAME' => _L('Submit notif.'),
			  'DESCRIPTION' => _L('Email template used for notifications sent to link the owner when the link is submited. You can edit the templates <a href="email_message.php">here</a>.'),
			  'CONFIG_GROUP' => '5',
			  'TYPE' => 'LKP',
			  'OPTIONS' => "SELECT `ID`, `TITLE` FROM `{$tables['email_tpl']['name']}` WHERE `TPL_TYPE` = '2'",
			  'REQUIRED' => '0'),
 		array('ID' => 'NTF_APPROVE_TPL',
			  'NAME' => _L('Link approve notif.'),
			  'DESCRIPTION' => _L('Email template used for notifications sent to link the owner when the link is approved. You can edit the templates <a href="email_message.php">here</a>.'),
			  'CONFIG_GROUP' => '5',
			  'TYPE' => 'LKP',
			  'OPTIONS' => "SELECT `ID`, `TITLE` FROM `{$tables['email_tpl']['name']}` WHERE `TPL_TYPE` = '2'",
			  'REQUIRED' => '0'),
 		array('ID' => 'NTF_REJECT_TPL',
			  'NAME' => _L('Link reject notif.'),
			  'DESCRIPTION' => _L('Email template used for notifications sent to link the owner when the link is rejected. You can edit the templates <a href="email_message.php">here</a>.'),
			  'CONFIG_GROUP' => '5',
			  'TYPE' => 'LKP',
			  'OPTIONS' => "SELECT `ID`, `TITLE` FROM `{$tables['email_tpl']['name']}` WHERE `TPL_TYPE` = '2'",
			  'REQUIRED' => '0'),
 		array('ID' => 'NTF_PAYMENT_TPL',
			  'NAME' => _L('Link payment notif.'),
			  'DESCRIPTION' => _L('Email template used for notifications sent to link the owner when a payment is received. You can edit the templates <a href="email_message.php">here</a>.'),
			  'CONFIG_GROUP' => '5',
			  'TYPE' => 'LKP',
			  'OPTIONS' => "SELECT `ID`, `TITLE` FROM `{$tables['email_tpl']['name']}` WHERE `TPL_TYPE` = '2'",
			  'REQUIRED' => '0'),

		#Admin
 		array('ID' => 'ENABLE_PAGERANK',
			  'NAME' => _L('Enable PageRank'),
			  'DESCRIPTION' => _L('Enables the Google PageRank calculation functionality.'),
			  'CONFIG_GROUP' => '6',
			  'TYPE' => 'LOG',
			  'REQUIRED' => '1'),
 		array('ID' => 'DEBUG',
			  'NAME' => _L('Enable debugging'),
			  'DESCRIPTION' => _L('PHP error logs are created in ./temp folder.'),
			  'CONFIG_GROUP' => '6',
			  'TYPE' => 'LOG',
			  'REQUIRED' => '1'),
 		array('ID' => 'LINKS_PER_PAGE',
			  'NAME' => _L('Links per page'),
			  'DESCRIPTION' => _L('The maximum number of links to be displayed on a page.'),
			  'CONFIG_GROUP' => '6',
			  'TYPE' => 'INT',
			  'REQUIRED' => '1'),
 		array('ID' => 'MAILS_PER_PAGE',
			  'NAME' => _L('Emails per page'),
			  'DESCRIPTION' => _L('The maximum number of Emails to be displayed on a page.'),
			  'CONFIG_GROUP' => '6',
			  'TYPE' => 'INT',
			  'REQUIRED' => '1'),
 		array('ID' => 'ENABLE_NEWS',
			  'NAME' => _L('Enable news'),
			  'DESCRIPTION' => _L('Retrieve the latest news from www.phplinkdirectory.com.'),
			  'CONFIG_GROUP' => '6',
			  'TYPE' => 'LOG',
			  'REQUIRED' => '1'),
		#Featured Links
 		array('ID' => 'FTR_ENABLE',
			  'NAME' => _L('Enable featured links'),
			  'DESCRIPTION' => _L('Enables the featured links functionality.'),
			  'CONFIG_GROUP' => '7',
			  'TYPE' => 'LOG',
			  'REQUIRED' => '1'),
 		array('ID' => 'FTR_MAX_LINKS',
			  'NAME' => _L('Max. featured links'),
			  'DESCRIPTION' => _L('The maximum number of featured links accepted per category.'),
			  'CONFIG_GROUP' => '7',
			  'TYPE' => 'INT',
			  'REQUIRED' => '1'),
		#Payment
 		array('ID' => 'PAY_ENABLE',
			  'NAME' => _L('Enable payments'),
			  'DESCRIPTION' => _L('Enables the payed links functionality.'),
			  'CONFIG_GROUP' => '9',
			  'TYPE' => 'LOG',
			  'REQUIRED' => '1'),
 		array('ID' => 'PAY_UM',
			  'NAME' => _L('Time unit'),
			  'DESCRIPTION' => _L('Time unit used for payed links validity period'),
			  'CONFIG_GROUP' => '9',
			  'TYPE' => 'LKP',
			  'OPTIONS' => $payment_um,
			  'REQUIRED' => '1'),
 		array('ID' => 'PAY_FEATURED_PLUS',
	          'NAME' => _L('Featured price +'),
	          'DESCRIPTION' => _L('Unit price for featured+ links.'),
	          'CONFIG_GROUP' => '9',
	          'TYPE' => 'NUM',
	          'REQUIRED' => '1'),
 		array('ID' => 'PAY_FEATURED',
			  'NAME' => _L('Featured price'),
			  'DESCRIPTION' => _L('Unit price for featured links. If greater than 0 users will be able to submit featured links.'),
			  'CONFIG_GROUP' => '9',
			  'TYPE' => 'NUM',
			  'REQUIRED' => '1'),
 		array('ID' => 'PAY_NORMAL_PLUS',
	          'NAME' => _L('Regular price +'),
	          'DESCRIPTION' => _L('Unit price for regular+ links.'),
	          'CONFIG_GROUP' => '9',
	          'TYPE' => 'NUM',
	          'REQUIRED' => '1'),
 		array('ID' => 'PAY_NORMAL',
			  'NAME' => _L('Regular price'),
			  'DESCRIPTION' => _L('Unit price for regular links. Set to 0 if you don\'t want to require any payment for regular links'),
			  'CONFIG_GROUP' => '9',
			  'TYPE' => 'NUM',
			  'REQUIRED' => '1'),
 		array('ID' => 'PAY_RECPR',
			  'NAME' => _L('Recpr. price'),
			  'DESCRIPTION' => _L('Unit price for links with reciprocal link. If set to 0 links with reciprocal link will not require any payment.'),
			  'CONFIG_GROUP' => '9',
			  'TYPE' => 'NUM',
			  'REQUIRED' => '1'),
 		array('ID' => 'PAY_ENABLE_FREE',
			  'NAME' => _L('Free links'),
			  'DESCRIPTION' => _L('Enables free links, but with a <b>nofollow</b> attribute.'),
			  'CONFIG_GROUP' => '9',
			  'TYPE' => 'LOG',
			  'REQUIRED' => '1'),
 		array('ID' => 'PAYPAL_ACCOUNT',
			  'NAME' => _L('PayPal account'),
			  'DESCRIPTION' => _L('Your PayPal account.'),
			  'CONFIG_GROUP' => '9',
			  'TYPE' => 'STR',
			  'REQUIRED' => '1'),
			  
		#reCaptcha
		array('ID' => 'RECAPTCHA_PUBLIC_KEY',
			  'NAME' => _L('reCaptcha Public Key'),
			  'DESCRIPTION' => _L('Enter your reCaptcha Public Key'),
			  'CONFIG_GROUP' => '10',
			  'TYPE' => 'STR',
			  'REQUIRED' => '1'),
 		array('ID' => 'RECAPTCHA_PRIVATE_KEY',
			  'NAME' => _L('reCaptcha Private Key'),
			  'DESCRIPTION' => _L('Enter your reCaptcha Private Key'),
			  'CONFIG_GROUP' => '10',
			  'TYPE' => 'STR',
			  'REQUIRED' => '1'),
 	);
?>