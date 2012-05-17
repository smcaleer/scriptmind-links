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
$tpl->assign('columns', array ('TITLE' => _L('Title'),
								'URL' => _L('URL'),
								'DESCRIPTION' => _L('Description'),
								'CATEGORY' => _L('Category'),
								'RECPR_URL' => _L('Recpr. Link URL'),
								'DATE_ADDED' => _L('Date Added'),
								'TITLE1' => _L('Title 1'),
								'TITLE2' => _L('Title 2'),
								'TITLE3' => _L('Title 3'),
								'TITLE4' => _L('Title 4'),
								'TITLE5' => _L('Title 5'),
								'URL1' => _L('URL 1'),
								'URL2' => _L('URL 2'),
								'URL3' => _L('URL 3'),
								'URL4' => _L('URL 4'),
								'URL5' => _L('URL 5'),
								'DESCRIPTION1' => _L('Description 1'),
								'DESCRIPTION2' => _L('Description 2'),
								'DESCRIPTION3' => _L('Description 3'),
								'DESCRIPTION4' => _L('Description 4'),
								'DESCRIPTION5' => _L('Description 5'),
								
));

if (defined('SORT_FIELD') && SORT_FIELD != '')
{
   $orderBy = ' ORDER BY '. (SORT_FIELD == 'CATEGORY' ? 'C.TITLE' : 'L.'.SORT_FIELD).' '.SORT_ORDER;
}
if ($_SESSION['is_admin'])
{
	$list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `STATUS` = '1'");
	$page = get_page($list_total);
	$tpl->assign('list_limit', LINKS_PER_PAGE);
	$tpl->assign('list_total', $list_total);
	$rs = $db->SelectLimit("SELECT L.*, ".$db->IfNull('C.TITLE', "'Top'")." AS `CATEGORY` FROM `{$tables['link']['name']}` AS `L` LEFT OUTER JOIN `{$tables['category']['name']}` AS `C` ON L.CATEGORY_ID = C.ID WHERE L.STATUS = '1' {$orderBy}", LINKS_PER_PAGE, LINKS_PER_PAGE * ($page - 1));
	$list = $rs->GetAssoc(true);
}
else
{
	$list_total = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `STATUS` = '1' AND (".str_replace ("ID", "CATEGORY_ID", $_SESSION['user_permission']).")");
	$page = get_page($list_total);
	$tpl->assign('list_limit', LINKS_PER_PAGE);
	$tpl->assign('list_total', $list_total);
	$rs = $db->SelectLimit("SELECT L.*, ".$db->IfNull('C.TITLE', "'Top'")." AS `CATEGORY` FROM `{$tables['link']['name']}` AS `L` LEFT OUTER JOIN `{$tables['category']['name']}` AS `C` ON L.CATEGORY_ID = C.ID WHERE L.STATUS = '1' AND (".str_replace ("ID", "L.CATEGORY_ID", $_SESSION['user_permission']).") {$orderBy}", LINKS_PER_PAGE, LINKS_PER_PAGE * ($page - 1));
	$list = $rs->GetAssoc(true);
}
$tpl->assign('list', $list);

$content = $tpl->fetch('admin/dir_approve_links.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('admin/main.tpl');
?>