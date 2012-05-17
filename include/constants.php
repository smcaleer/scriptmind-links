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
// this is to block empty useragent and remote submitals 0 equals not and 1 = yes
$ALLOW_EMPTY_USERAGENT = 0;
$ALLOW_FOREIGN_REFERER = 0;

$email_tpl_types = array( '1' => _L('Emailer'), '2' => _L('Link Owner Notif.'), '3' => _L('Email and Add Link'));
$payment_um = array('1' => _L('Month'),'2' => _L('Trimester'), '3' => _L('Semester'), '4' => _L('Year'), '5' => _L('Unlimited'));
$link_type_int = array( 'none' => 0, 'free' => 1, 'normal' => 2, 'reciprocal' => 3, 'featured' => 4, 'normal_plus' => 5, 'featured_plus' => 6);
$link_type_str = array( 0 => _L('None'), 1 => _L('Free'), 2 => _L('Normal'), 3 => _L('Reciprocal'), 4 => _L('Featured'), 5 => _L('Normal+'), 6 => _L('Featured+'));
$notif_msg = array(
	'submit' => array(
		'SUBJECT' => 'New link submited at {MY_SITE_URL}',
		"BODY" => "Title: {LINK_TITLE}\n" .
				  "URL: {LINK_URL}\n" .
				  "PageRank: {LINK_PAGERANK}\n" .
				  "Description:\n {LINK_DESCRIPTION}\n" .
				  "Owner Name: {LINK_OWNER_NAME}\n" .
				  "Owner Email: {LINK_OWNER_EMAIL}\n" .
				  "Reciprocal URL: {LINK_RECPR_URL}\n" .
				  "Reciprocal PageRank: {LINK_RECPR_PAGERANK}\n"
	),
	'payment' => array(
		'SUBJECT' => 'New {PAYMENT_SUCCESS} payment at {MY_SITE_URL}',
		"BODY" => "Link Title: {LINK_TITLE}\n" .
				  "Link URL: {LINK_URL}\n" .
				  "Payer Name: {PAYMENT_NAME}\n" .
				  "Payer Email: {PAYMENT_EMAIL}\n" .
				  "Unit price: {PAYMENT_AMOUNT}\n" .
				  "Quantity: {PAYMENT_QUANTITY}\n" .
				  "Amount to be payed: {PAYMENT_TOTAL}\n" .
				  "Amount payed: {PAYMENT_PAYED_TOTAL}\n"
	),
);
?>
