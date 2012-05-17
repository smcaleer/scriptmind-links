<?php
/**
# ######################################################################
# Project:     gplLD: Version 0.1
#
# **********************************************************************
# Copyright (C) Utkarsh Kukreti (http://www.gplld.com) 
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
# @link           http://www.gplld.com/
# @copyright      Utkarsh Kukreti (http://utkar.sh/)
# @package        gplLD
# ######################################################################
*/

require_once 'init.php';
header('Content-type: text/xml');
// num of records
$count = 10;
if ($_REQUEST['type'])
$type = $_REQUEST['type'];
if (isset($_REQUEST['count']) && is_numeric ($_REQUEST['count']))
	$count = intval($_REQUEST['count']);
	
$interval = 7;
$days = $count*$interval;

$currentTime = time();
switch($type)
{
	case 'link-submits':		
		for($i=0; $i<$count; $i++)
		{
			$days = $i*$interval;
			$q = "SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE DATE_SUB( CURDATE( ) , INTERVAL " . ($days+$interval) . " DAY ) <= DATE_ADDED AND DATE_SUB( CURDATE(), INTERVAL " . ($days) . " DAY ) >= DATE_ADDED";
			$toPrint[$i] = $db->GetOne($q);
		}
		
		echo '<graph caption="Link Submits" subcaption="Last ' . $days . ' Days" xAxisName="Date" yAxisName="Link Submits" decimalPrecision="2" formatNumberScale="0" numberPrefix="" showNames="0" showValues="0">';
		for($i=$count-1; $i>=0; $i--)
		{
			$startDate = date("F j, Y", $currentTime-86400*($i+1)*$interval);
			$endDate = date("F j, Y", $currentTime-86400*($i)*$interval);
			$caption = "$startDate - $endDate";
			echo '<set name="' . $caption . '" value = "' . (isset($toPrint[$i])?$toPrint[$i]:'0') . '" hoverText="' . $caption . '"/>';
		}
		echo '</graph>';
	break;
	
	case 'link-sales':
		for($i=0; $i<$count; $i++)
		{
			$days = $i*$interval;
			$q = "SELECT SUM(PAYED_TOTAL) FROM `{$tables['payment']['name']}` WHERE DATE_SUB( CURDATE( ) , INTERVAL " . ($days+$interval) . " DAY ) <= PAY_DATE AND DATE_SUB( CURDATE(), INTERVAL " . ($days) . " DAY ) >= PAY_DATE";
			$toPrint[$i] = $db->GetOne($q);
		}
		
		echo '<graph caption="Link Sales" subcaption="Last ' . $days . ' Days" xAxisName="Date" yAxisName="Link Sales" decimalPrecision="2" formatNumberScale="0" numberPrefix="$" showNames="0" showValues="0">';
		for($i=$count-1; $i>=0; $i--)
		{
			$startDate = date("F j, Y", $currentTime-86400*($i+1)*$interval);
			$endDate = date("F j, Y", $currentTime-86400*($i)*$interval);
			$caption = "$startDate - $endDate";
			echo '<set name="' . $caption . '" value = "' . (isset($toPrint[$i])?$toPrint[$i]:'0') . '" hoverText="' . $caption . '"/>';
		}
		echo '</graph>';
	break;
}

?>