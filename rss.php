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

if (!ENABLE_RSS)
{
   @ header('Location: index.php');
   @ exit;
}
$expire_where = "AND (`EXPIRY_DATE` >= ".$db->DBDate(time())." OR `EXPIRY_DATE` IS NULL)";

if (!empty ($_REQUEST['p']))
{
	switch ($_REQUEST['p'])
   {
		case 'd':
			$sql = "SELECT * FROM `{$tables['link']['name']}` WHERE STATUS = '2' {$expire_where} ORDER BY `DATE_ADDED` DESC LIMIT 0, ".LINKS_TOP;
			$tpl->assign('title', ' - Latest Links');
			break;
		case 'h':
			$sql = "SELECT * FROM `{$tables['link']['name']}` WHERE `STATUS` = '2' {$expire_where} ORDER BY `HITS` DESC LIMIT 0, ".LINKS_TOP;
			$tpl->assign('title', ' - Top Hits');
			break;
	}
}
elseif (!empty ($_REQUEST['q']))
{
	$tpl->assign('title', ' - Search results');
	$tpl->assign('description', ' Search results for: '.htmlspecialchars($_REQUEST['q']));
	$q = $db->qstr('%'.preg_replace('`\s+`','%', $_REQUEST['q']).'%');
	$sql = "SELECT * FROM `{$tables['link']['name']}` WHERE `STATUS` = '2' AND (`URL` LIKE {$q} OR `TITLE` LIKE {$q} OR `DESCRIPTION` LIKE {$q}) {$expire_where} LIMIT ".LINKS_PER_PAGE;

}
elseif ($id = get_category())
{
	$sql = "SELECT * FROM `{$tables['link']['name']}` WHERE `STATUS` = '2' AND `CATEGORY_ID` = ".$db->qstr($id)." {$expire_where} LIMIT ".LINKS_PER_PAGE;
	$path = get_path($id);
	$url = '';
	$title = ' - ';
	for ($i = 1; $i < count($path); $i++)
   {
		if (ENABLE_REWRITE)
      {
			$url .= $path[$i]['TITLE_URL'].'/';
		}
		if ($i > 1)
      {
			$title .= ' &gt; ';
		}
		$title .= $path[$i]['TITLE'];
	}
	if (!ENABLE_REWRITE)
   {
		$url = 'index.php?c='.$id;
	}

	$tpl->assign('title', xml_utf8_encode($title));
	$tpl->assign('description', xml_utf8_encode($path[count($path)]['DESCRIPTION']));
}
$tpl->assign('url', htmlspecialchars('http://'.$_SERVER['SERVER_NAME'].DOC_ROOT.'/'.$url));

if ($limit > 0)
{
	$rs = $db->SelectLimit($sql, $limit);
	$links = $rs->GetRows();
}
else
{
	$links = $db->GetAll($sql);
}

$n = count ($links);
for ($i = 0; $i < $n; $i++)
{
	$links[$i]['TITLE'] = xml_utf8_encode($links[$i]['TITLE']);
	$links[$i]['DESCRIPTION'] = xml_utf8_encode($links[$i]['DESCRIPTION']);
	$links[$i]['URL'] = xml_utf8_encode($links[$i]['URL']);
	$links[$i]['RECPR_URL'] = xml_utf8_encode($links[$i]['RECPR_URL']);
	$links[$i]['OWNER_NAME'] = xml_utf8_encode($links[$i]['OWNER_NAME']);
	$links[$i]['OWNER_EMAIL'] = xml_utf8_encode($links[$i]['OWNER_EMAIL']);
}
if ($n > 0)
	$tpl->assign('links', $links);

@ header('Content-type: application/xml');
echo $tpl->fetch('rss2.tpl');
?>