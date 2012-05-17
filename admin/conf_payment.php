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

if ($_REQUEST['action'])
   list ($action, $id, $val) = split(':', $_REQUEST['action']);

switch ($action)
{
   case 'D' : //Delete
   case 'd' :
      if (!$db->Execute("DELETE FROM `{$tables['payment']['name']}` WHERE `ID` = ".$db->qstr($id)))
      {
         $tpl->assign('error', $error);
         $tpl->assign('sql_error', $db->ErrorMsg());
      }
      break;
}

$tpl->assign('columns',
			array (
				'TITLE' => _L('Link Title'),
				'LINK_TYPE' => _L('Link Type'),
				'STATUS' => 'Link Status',
				'P_UM' => _L('Unit'),
				'P_QUANTITY' => _L('Quantity'),
				'P_AMOUNT' => _L('Price'),
				'P_TOTAL' => _L('Total'),
				'P_CONFIRMED' => 'Payment Status',
				'P_PAYED_TOTAL' => _L('Payed'),
				'P_PAY_DATE' => _L('Date'),
				'P_CONFIRM_DATE' => _L('Pay Date'),
			));

if (defined('SORT_FIELD') && SORT_FIELD != '')
{
	$orderBy = ' ORDER BY '. SORT_FIELD.' '.SORT_ORDER;
}
$list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['payment']['name']}`");

$page = get_page($list_total);
$tpl->assign('list_limit', LINKS_PER_PAGE);
$tpl->assign('list_total', $list_total);
$pfields = '';

foreach($tables['payment']['fields'] as $f => $v)
{
	$pfields .= "P.$f AS P_$f, ";
}

$rs = $db->SelectLimit("SELECT $pfields L.*, ".$db->IfNull('C.TITLE', "'Top'")." AS `CATEGORY` FROM `{$tables['payment']['name']}` P LEFT OUTER JOIN `{$tables['link']['name']}` L ON P.LINK_ID = L.ID LEFT OUTER JOIN `{$tables['category']['name']}` AS `C` ON L.CATEGORY_ID = C.ID {$orderBy}", LINKS_PER_PAGE, LINKS_PER_PAGE * ($page -1));
$list = $rs->GetAssoc(true);

$tpl->assign('list', $list);
$tpl->assign('payment_um', $payment_um);
$tpl->assign('link_type_str', $link_type_str);
$tpl->assign('stats', array (0 => _L('Inactive'), 1 => _L('Pending'), 2 => _L('Active'),));

$content = $tpl->fetch('admin/conf_payment.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('admin/main.tpl');
?>