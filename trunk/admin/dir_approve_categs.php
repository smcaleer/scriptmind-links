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

$tpl->assign('stats', array (0 => _L('Inactive'), 1 => _L('Pending'), 2 => _L('Active'),));
$tpl->assign('columns', array ('TITLE' => _L('Title'), 'DESCRIPTION' => _L('Description'), 'PARENT' => _L('Parent'), 'DATE_ADDED' => _L('Date Added')));

if (defined('SORT_FIELD') && SORT_FIELD != '')
{
   $orderBy = ' ORDER BY '. (SORT_FIELD == 'CATEGORY' ? 'P.TITLE' : 'C.'.SORT_FIELD).' '.SORT_ORDER;
}

$list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `STATUS` = '1'");
$page = get_page($list_total);
$tpl->assign('list_limit', LINKS_PER_PAGE);
$tpl->assign('list_total', $list_total);

$rs = $db->SelectLimit("SELECT C.*, ".$db->IfNull('P.TITLE', "'Top'")." AS `PARENT` FROM `{$tables['category']['name']}` AS `C` LEFT OUTER JOIN `{$tables['category']['name']}` AS `P` ON C.PARENT_ID = P.ID WHERE C.STATUS = '1' {$orderBy}", LINKS_PER_PAGE, LINKS_PER_PAGE * ($page - 1));
$list = $rs->GetAssoc(true);
$tpl->assign('list', $list);

$content = $tpl->fetch('admin/dir_approve_categs.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('admin/main.tpl');
?>