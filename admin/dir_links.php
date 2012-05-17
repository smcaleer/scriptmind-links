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

unset($c);
if($_REQUEST['c'])
{
	$c = $_REQUEST['c'];
	$_SESSION[SCRIPT_NAME]['c'] = $c;
}
elseif($_SESSION[SCRIPT_NAME]['c'])
{
	$c = $_SESSION[SCRIPT_NAME]['c'];
}
if($c)
{
	$where = " WHERE CATEGORY_ID = ".$db->qstr($c);
}
else
{
	if (isset ($where))
   {
		$where .= ' AND '.($_REQUEST['f'] == '1' ? '' : 'NOT').$featured_where;
	}
   else
   {
		$where = 'WHERE '.($_REQUEST['f']=='1' ? '' : 'NOT').$featured_where;
	}
}
$tpl->assign('featured', $_REQUEST['f']=='1');
$tpl->assign('ENABLE_REWRITE', ENABLE_REWRITE);
$tpl->assign('stats', array (0 => _L('Inactive'), 1 => _L('Pending'), 2 => _L('Active'),));
$tpl->assign('valid', array (0 => _L('Broken'), 1 => _L('Unknown'), 2 => _L('Ok'),));
$columns = array ('TITLE' => _L('Title'), 'PAGERANK' => _L('PR'), 'URL' => _L('URL'));

if (PAY_ENABLE)
{
	$columns = array_merge ($columns, array('LINK_TYPE' => _L('Type')));
	$tpl->assign('link_type_str', $link_type_str);
}
$columns = array_merge ($columns, array('CATEGORY' => _L('Category'), 'STATUS' => _L('Status'), 'HITS' => _L('Hits'), 'DATE_ADDED' => _L('Date Added')));
$tpl->assign('columns', $columns);
$tpl->assign('col_count', count ($columns) + 2);
$orderBy = ' ORDER BY FEATURED DESC';

if (defined('SORT_FIELD') && SORT_FIELD != '')
{
	$orderBy .= ', '. (SORT_FIELD == 'CATEGORY' ? 'C.TITLE' : 'L.'.SORT_FIELD).' '.SORT_ORDER;
}

if ($_SESSION['is_admin'])
{
	$list_total = $db->GetOne("SELECT COUNT(*) FROM {$tables['link']['name']} $where");
	$page = get_page($list_total);
	$tpl->assign('list_limit', LINKS_PER_PAGE);
	$tpl->assign('list_total', $list_total);
	$sql = "SELECT L.*, ".$db->IfNull('C.TITLE', "'Top'")." AS CATEGORY FROM {$tables['link']['name']} AS L LEFT OUTER JOIN {$tables['category']['name']} AS C ON L.CATEGORY_ID = C.ID $where ".$orderBy;
	$rs = $db->SelectLimit($sql , LINKS_PER_PAGE, LINKS_PER_PAGE * ($page -1));
	$list = $rs->GetAssoc(true);
}
else
{
	$list_total = $db->GetOne("SELECT COUNT(*) FROM {$tables['link']['name']} $where AND (".str_replace("ID","CATEGORY_ID",$_SESSION['user_permission']).")");
	$page = get_page($list_total);
	$tpl->assign('list_limit', LINKS_PER_PAGE);
	$tpl->assign('list_total', $list_total);
	$sql = "SELECT L.*, ".$db->IfNull('C.TITLE', "'Top'")." AS CATEGORY FROM {$tables['link']['name']} AS L LEFT OUTER JOIN {$tables['category']['name']} AS C ON L.CATEGORY_ID = C.ID $where AND (".str_replace("ID","L.CATEGORY_ID",$_SESSION['user_permission']).") ".$orderBy;
	$rs = $db->SelectLimit($sql , LINKS_PER_PAGE, LINKS_PER_PAGE * ($page -1));
	$list = $rs->GetAssoc(true);
}

$tpl->assign('list', $list);
$cid= get_category($_SERVER['REQUEST_URI']);

if ($cid == 0)
{
	$rss_link = false;
}
else
{
	$rss_link = true;
	$tpl->assign('category', $cid);
}
$tpl->assign('rss_link', $rss_link);
$content = $tpl->fetch('admin/dir_links.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('admin/main.tpl');
?>