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

$tpl->assign('ENABLE_REWRITE', ENABLE_REWRITE);
$tpl->assign('admin_user', array (0 => _L('Editor'), 1 => _L('Administrator')));
$tpl->assign('yes_no', array (0 => _L('No'), 1 => _L('Yes')));
$tpl->assign('columns', array ('LOGIN' => _L('Login'), 'NAME' => _L('Name'), 'EMAIL' => _L('Email'), 'ADMIN' => _L('Admin'), 'SUBMIT_NOTIF' => _L('Submit Notif'), 'PAYMENT_NOTIF' => _L('Payment Notif')));
$tpl->assign('current_user_id',$_SESSION['user_id']);
$tpl->assign('current_user_is_admin',$_SESSION['is_admin']);

if (defined ('SORT_FIELD') && SORT_FIELD != '')
{
	$orderBy = ' ORDER BY '. SORT_FIELD.' '.SORT_ORDER;
}
if ($_SESSION['is_admin'])
{
	$list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['user']['name']}`");
	$page = get_page($list_total);
	$tpl->assign('list_limit', LINKS_PER_PAGE);
	$tpl->assign('list_total', $list_total);
	$rs = $db->SelectLimit("SELECT * FROM `{$tables['user']['name']}` {$orderBy}", LINKS_PER_PAGE, LINKS_PER_PAGE * ($page -1));
	$list = $rs->GetAssoc(true);
	$tpl->assign('list', $list);
}
else
{
	$list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['user']['name']}` WHERE `ADMIN` = '0'");
	//$list_total = $db->GetOne("SELECT COUNT(*) FROM {$tables['user']['name']} WHERE ID = ".$_SESSION['user_id']);
	$page = get_page($list_total);
	$tpl->assign('list_limit', LINKS_PER_PAGE);
	$tpl->assign('list_total', $list_total);
	$rs = $db->SelectLimit("SELECT * FROM `{$tables['user']['name']}` WHERE `ADMIN` = '0' {$orderBy}", LINKS_PER_PAGE, LINKS_PER_PAGE * ($page -1));

	$list = $rs->GetAssoc(true);
	$tpl->assign('list', $list);
}
$content = $tpl->fetch('admin/conf_users.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('admin/main.tpl');
?>