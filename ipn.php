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

$paypal_host = 'www.paypal.com';
$paypal_path = '/cgi-bin/webscr';
$ipn_data = array();

$pid = $_GET['pid'];

$data = array();

$post_string = '';
foreach ($_POST as $field => $value) {
	$ipn_data["$field"] = $value;
	$post_string .= $field.'='.urlencode($value).'&';
}
$post_string .= "cmd=_notify-validate"; // append ipn command

// open the connection to paypal
$fp = fsockopen($paypal_host, "80", $err_num, $err_str, 30);
if (!$fp) {

	// could not open the connection.  If loggin is on, the error message
	// will be in the log.
	$last_error = "fsockopen error no. $errnum: $errstr";
	log_ipn_results(false);
	exit;

} else {

	// Post the data back to paypal
	fputs($fp, "POST $paypal_path HTTP/1.1\r\n");
	fputs($fp, "Host: $paypal_host\r\n");
	fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
	fputs($fp, "Content-length: ".strlen($post_string)."\r\n");
	fputs($fp, "Connection: close\r\n\r\n");
	fputs($fp, $post_string."\r\n\r\n");

	// loop through the response from the server and append to variable
	while (!feof($fp)) {
		$ipn_response .= fgets($fp, 1024);
	}

	fclose($fp); // close connection

}

if (eregi("VERIFIED", $ipn_response)) {

	// Valid IPN transaction.
	log_ipn_results(true);
	exit;

} else {

	// Invalid IPN transaction.  Check the log for details.
	$last_error = 'IPN Validation Failed.';
	log_ipn_results(false);
	exit;

}

function log_ipn_results($success) {
	global $ipn_data, $last_error, $ipn_response;
      // Timestamp
      $text = '['.date('m/d/Y g:i A').'] - ';

      // Success or failure being logged?
      if ($success) $text .= "SUCCESS!\n";
      else $text .= 'FAIL: '.$last_error."\n";

      // Log the POST variables
      $text .= "IPN POST Vars from Paypal:\n";
      foreach ($ipn_data as $key=>$value) {
         $text .= "$key=$value, ";
         $raw /= "$key\t$value\n";
      }
      $data['email'] = $ipn_data['payer_email'];
      $data['name'] = $ipn_data['last_name'].' '.$ipn_data['first_name'];
      $data['link_id'] = $ipn_data['item_number'];
      $data['quantity'] = $ipn_data['quantity'];#mc_gross
      $data['total'] = $ipn_data['mc_gross'];
      update_link_payment($_GET['pid'], $data, $success, $raw);
      // Log the response from the paypal server
      $text .= "\nIPN Response from Paypal Server:\n ".$ipn_response;
      // Write to log
      $fp=fopen(INSTALL_PATH.'temp/ipn_log.txt','a');
      fwrite($fp, $text . "\n\n");
      fclose($fp);  // close file
 }
?>