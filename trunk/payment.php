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
//javababble script taken from cci security script originally developed by Mike Parnik
// included here by James Marsh aka doofus from the CCI forums 
// There is no need for the full script if someone wants to know more contact dawzz.space@gmail.com
##### Encryption/Encoding Variables 

$javababble = 0;            # 1 = Use Encoding/Encrypting (Must be on for any), 0 = Don't 
$javaencrypt = 0;            # Do actual encrypting of HTML, not just escaping (warning: may slow display) 
$preservehead = 0;            # 1 = Only encode/encrypt between BODY tags, 0 = encode/encrypt whole document 


function CCIJavaBabble($myoutput) { 
  global $mycrypto, $myalpha2, $javaencrypt, $preservehead; 
  $s = $myoutput; 
  $s = ereg_replace("\n","",$s); 

  if ($preservehead) {  
    eregi("(^.+<body[^>]*>)",$s,$chunks); 
    $outputstring = $chunks[1]; 
    eregi_replace($headpart,"",$s); 

    eregi("(</body[^>]*>.*)",$s,$chunks); 
    $outputend = $chunks[1]; 
    eregi_replace($footpart,"",$s); 
  } else { 
    $outputstring = ""; 
    $outputend = ""; 
  } 
  
  if ($javaencrypt) { 
    $s = strtr($s,$myalpha2,$mycrypto); 
    $s = rawurlencode($s); 
    $outputstring .= "<script>var cc=unescape('$s'); "; 
    $outputstring .= "var index = document.cookie.indexOf('" . md5($_SERVER["REMOTE_ADDR"] . $_SERVER["SERVER_ADDR"]) . "='); " . 
      "var aa = '$myalpha2'; " . 
      "if (index > -1) { " . 
      "  index = document.cookie.indexOf('=', index) + 1; " . 
      "  var endstr = document.cookie.indexOf(';', index); " . 
      "  if (endstr == -1) endstr = document.cookie.length; " . 
      "  var bb = unescape(document.cookie.substring(index, endstr)); " . 
      "} " . 
      "cc = cc.replace(/[$myalpha2]/g,function(str) { return aa.substr(bb.indexOf(str),1) }); document.write(cc);"; 
  } else { 
    $outputstring .= "<script>document.write(unescape('" . rawurlencode($s) . "'));"; 
  } 
  $outputstring .= "</script><noscript>You must enable Javascript in order to view this webpage.</noscript>" . $outputend; 
        
  return $outputstring; 
} 

if ($javababble) { 
  if ($javaencrypt) { 
    $myalpha = array_merge(range("a","z"),range("A","Z"),range("0","9")); 
    $myalpha2 = implode("",$myalpha); 
    shuffle($myalpha); 
    $mycrypto = implode("",$myalpha); 
    setcookie(md5($_SERVER["REMOTE_ADDR"] . $_SERVER["SERVER_ADDR"]),$mycrypto); 
    unset($myalpha); 
  } 
  ob_start("cciJavaBabble"); 
} 
//javababble
require_once 'init.php';

if (isset ($_REQUEST['id']))
{
   $id     = $_REQUEST['id'];
   $action = 'pay';
}
elseif (isset ($_REQUEST['ID']))
{
   $id     = $_REQUEST['ID'];
   $action = 'pay';
}
elseif ($_REQUEST['payed'])
{
   $id     = $_REQUEST['payed'];
   $action = 'payed';
}
elseif ($_REQUEST['canceled'])
{
   $id     = $_REQUEST['canceled'];
   $action = 'canceled';
}

$id = (isset ($id) ? trim ($id) : 0);
$id = preg_replace ('`(id[_]?)`', '', $id);
$id = (preg_match ('`^[\d]+$`', $id) ? intval ($id) : 0);

if (!empty ($id) && preg_match('`[\d]+`', $id))
{
	$data = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($id));
}
if (empty($data['ID'])) {
	echo $tpl->fetch('payment.tpl');
	@ exit ();
}

$price = array ();
if (FTR_ENABLE == '1' && PAY_FEATURED > 0)
{
	$price[$link_type_int['featured']] = PAY_FEATURED;
}
if (FTR_ENABLE == '1' && PAY_FEATURED_PLUS > 0)
   $price[$link_type_int['featured_plus']] = PAY_FEATURED_PLUS;   
if (PAY_NORMAL > 0)
{
	$price[$link_type_int['normal']] = PAY_NORMAL;
	if (PAY_ENABLE_FREE)
   {
		$price[$link_type_int['free']] = 0;
	}
}
if (PAY_NORMAL_ADV > 0)
{
   $price[$link_type_int['normal_plus']] = PAY_NORMAL_PLUS;
}
if (PAY_RECPR > 0) {
	$price[$link_type_int['reciprocal']] = PAY_RECPR;
}

if($action=='pay')
{
	if (empty ($_REQUEST['submit']))
   {
		if (!empty ($_SERVER['HTTP_REFERER']))
			$_SESSION['return'] = $_SERVER['HTTP_REFERER'];

		SmartyValidate :: connect($tpl);
		SmartyValidate :: register_form('pay_link', true);
		SmartyValidate :: register_validator('v_quantity', 'quantity', 'isInt', false, false, 'trim', 'pay_link');
	}
   else
   {
		SmartyValidate :: connect($tpl);
		if (SmartyValidate :: is_valid($_REQUEST, 'pay_link'))
      {
			$pay_data = array();
			$pay_id = $db->GenID($tables['payment']['name'].'_SEQ');
			$pay_data['ID'] = $pay_id;
			$pay_data['LINK_ID'] = $data['ID'];
			$pay_data['IPADDRESS'] = get_client_ip();
			$pay_data['QUANTITY'] = $_REQUEST['quantity'];
			$pay_data['AMOUNT'] = $price[$data['LINK_TYPE']];
			$pay_data['TOTAL'] = (int)$pay_data['QUANTITY'] * (float)$pay_data['AMOUNT'];
			$pay_data['UM'] = PAY_UM;
			$pay_data['PAY_DATE'] = gmdate('Y-m-d H:i:s');
			$pay_data['CONFIRMED'] = -1;
			if (db_replace('payment', $pay_data, 'ID') > 0)
         {
				$action = 'paypal';
				$tpl->assign('PAYMENT', $pay_data);
			}
         else
         {
				$tpl->assign('error', true);
			}
		}
	}
	$quantity  = (!empty ($_REQUEST['quantity']) && preg_match ('`^[\d]+$`', $_REQUEST['quantity']) ? intval ($_REQUEST['quantity']) : 1);
}

$tpl->assign('quantity', $quantity);
$tpl->assign('price', $price);
$tpl->assign('action', $action);

$path = array ();
$path[] = array ('ID' => '0', 'TITLE' => _L(SITE_NAME), 'TITLE_URL' => DOC_ROOT, 'DESCRIPTION' => SITE_DESC);
$path[] = array ('ID' => '0', 'TITLE' => _L('Link Payment'), 'TITLE_URL' => '', 'DESCRIPTION' => _L('Submit a new link to the directory '));
$tpl->assign('path', $path);

$tpl->assign($data);

$tpl->assign('payment_um', $payment_um);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

if ($action == 'paypal')
{
   echo $tpl->fetch('paypal.tpl');
}
else
{
   echo $tpl->fetch('payment.tpl');
}
?>