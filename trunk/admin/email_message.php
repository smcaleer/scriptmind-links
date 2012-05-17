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
{
	list ($action, $id) = split (':', $_REQUEST['action']);
}

$tpl->assign('columns', array ('TITLE' => _L('Title'), 'TPL_TYPE' => _L('Type'), 'SUBJECT' => _L('Subject')));

if (defined ('SORT_FIELD') && SORT_FIELD != '')
{
	$orderBy = ' ORDER BY '. SORT_FIELD.' '.SORT_ORDER;
}
$list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['email_tpl']['name']}`");
$page = get_page($list_total);
$tpl->assign('list_limit', LINKS_PER_PAGE);
$tpl->assign('list_total', $list_total);
$rs = $db->SelectLimit("SELECT * FROM `{$tables['email_tpl']['name']}` {$orderBy}", LINKS_PER_PAGE, LINKS_PER_PAGE * ($page - 1));
$list = $rs->GetAssoc(true);
$tpl->assign('list', $list);
$tpl->assign('tpl_types', $email_tpl_types);
$content = $tpl->fetch('admin/email_message.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('admin/main.tpl');
?>