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

define('DIR_LPP', 20);
$sort_cols = array ( 'P' => 'PAGERANK', 'H' => 'HITS', 'A' => 'TITLE');
$sort_ord  = array ( 'P' => 'DESC'    , 'H' => 'DESC', 'A' => 'ASC');

//	Paging 1
$page = (!empty ($_REQUEST['p']) && preg_match ('`^[\d]+$`', $_REQUEST['p']) ? intval ($_REQUEST['p']) : 1);;

if ($page != 1)
{
   $min = PAGER_LPP * $page - (PAGER_LPP);
   $max = PAGER_LPP * $page;
}
else
{
   $min = 0;
   $max = PAGER_LPP;
}
$limit = " LIMIT {$min}," . PAGER_LPP;
//	End Paging 1

$getSort = (!empty ($_REQUEST['s']) ? trim ($_REQUEST['s']) : (!empty ($URLvariables['s']) ? $URLvariables['s'] : ''));
if (array_key_exists ($getSort, $sort_cols))
{
   $sort = $getSort;
}
else
{
   $sort = DEFAULT_SORT;
}

if ((ENABLE_PAGERANK != 1 || SHOW_PAGERANK != 1) && $sort == 'P')
   $sort = 'H';

$tpl->assign('sort', $sort);

$path = array();
$path[] = array ('ID' => '0', 'TITLE' => _L(SITE_NAME), 'TITLE_URL' => DOC_ROOT, 'DESCRIPTION' => SITE_DESC);

if (FTR_ENABLE)
{
	$feat_where = "AND (`FEATURED` = '0')";
}
$expire_where = "AND (`EXPIRY_DATE` >= ".$db->DBDate(time())." OR `EXPIRY_DATE` IS NULL)";

$available_options = array ('d' => _L('Latest Links'), 'h' => _L('Top Hits'));

if (!empty ($_REQUEST['p']) && array_key_exists ($_REQUEST['p'], $available_options))
{
	switch ($_REQUEST['p'])
   {
		case 'd':
			$links = $db->GetAll("SELECT * FROM `{$tables['link']['name']}` WHERE `STATUS` = '2' {$expire_where} ORDER BY `DATE_ADDED` DESC LIMIT 0, ".LINKS_TOP);
			$path[] = array ('ID' => '0', 'TITLE' => _L('Latest Links'), 'TITLE_URL' => '', 'DESCRIPTION' => '');
			break;
		case 'h':
      default :
			$links = $db->GetAll("SELECT * FROM `{$tables['link']['name']}` WHERE `STATUS` = '2' {$expire_where} ORDER BY `HITS` DESC LIMIT 0, ".LINKS_TOP);
			$path[] = array ('ID' => '0', 'TITLE' => _L('Top Hits'), 'TITLE_URL' => '', 'DESCRIPTION' => '');
			break;
	}
	$tpl->assign('p', $_REQUEST['p']);

}
elseif (isset ($_REQUEST['q']) && !empty ($_REQUEST['q']) && strlen (trim ($_REQUEST['q'])) > 2)
{
	$q = $db->qstr('%'.preg_replace('`\s+`','%', trim ($_REQUEST['q'])).'%');
	if (FTR_ENABLE)
   {
		$feat_links = $db->GetAll("SELECT * FROM `{$tables['link']['name']}` WHERE `STATUS` = '2' AND (`URL` LIKE {$q} OR `TITLE` LIKE {$q} OR `DESCRIPTION` LIKE {$q}) AND `FEATURED` = '1' {$expire_where} ORDER BY `EXPIRY_DATE` DESC");
		$tpl->assign('feat_links', $feat_links);
	}

	$links = $db->GetAll("SELECT * FROM `{$tables['link']['name']}` WHERE `STATUS` = '2' AND (`URL` LIKE {$q} OR `TITLE` LIKE {$q} OR `DESCRIPTION` LIKE {$q}) {$feat_where} {$expire_where} ORDER BY {$sort_cols[$sort]} {$sort_ord[$sort]}");

	$categs = array();
	$path[] = array ('ID' => '0', 'TITLE' => _L('Search Results'), 'TITLE_URL' => '', 'DESCRIPTION' => _L('Search results for: ').$_REQUEST['q']);
	$tpl->assign('qu', rawurlencode (trim ($_REQUEST['q'])));
}
else
{
	$id = get_category();
	if (!$tpl->is_cached('main.tpl', $id))
   {
		$path = get_path($id);

		if (FTR_ENABLE)
      {
			$feat_links = $db->GetAll("SELECT * FROM `{$tables['link']['name']}` WHERE `STATUS` = '2' AND `CATEGORY_ID` = ".$db->qstr($id)." AND `FEATURED` = 1 {$expire_where} ORDER BY `EXPIRY_DATE` DESC");
			$tpl->assign('feat_links', $feat_links);
		}

		// Paging 3
      $count = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `STATUS` = '2' AND `CATEGORY_ID` = ".$db->qstr($id)." {$feat_where} {$expire_where}");
		// End Paging 3

      $links = $db->GetAll("SELECT * FROM `{$tables['link']['name']}` WHERE `STATUS` = '2' AND `CATEGORY_ID` = ".$db->qstr($id)." {$feat_where} {$expire_where} ORDER BY `{$sort_cols[$sort]}` {$sort_ord[$sort]} {$limit}");
		$rs = $db->Execute("SELECT * FROM `{$tables['category']['name']}` WHERE `STATUS` = 2 AND `PARENT_ID` = ".$db->qstr($id)." ORDER BY `TITLE`");
		while (!$rs->EOF)
      {
			$row = $rs->FetchRow();
			if ($id == 0 && CATS_PREVIEW > 0)
         {
				$rs2 = $db->SelectLimit("SELECT * FROM `{$tables['category']['name']}` WHERE `STATUS` = '2' AND `SYMBOLIC` <> 1 AND `PARENT_ID` = ".$db->qstr($row['ID'])." ORDER BY `TITLE` ASC", CATS_PREVIEW);
				$row['SUBCATS'] = $rs2->GetRows();
				$rs2->Close();
			}
			if (ENABLE_REWRITE && empty ($row['TITLE_URL']))
         {
				$row['TITLE_URL'] = preg_replace ('`[^\w_-]`', '_', $row['TITLE']);
				$row['TITLE_URL'] = str_replace  ('__', '_', $row['TITLE_URL']);
			}

         if ($row['SYMBOLIC'] == 1)
         {
            $row['ID'] = $row['SYMBOLIC_ID'];
            $tempcat = $db->GetRow("SELECT * FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($row['SYMBOLIC_ID']));
            if (empty ($row['TITLE']))
               $row['TITLE'] = $tempcat['TITLE'];

            $row['TITLE'] = "@" . $row['TITLE'];

            if (ENABLE_REWRITE == 1)
               $row['TITLE_URL'] = construct_mod_rewrite_path($row['SYMBOLIC_ID']);

            $row['COUNT'] = $db->GetOne("SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `STATUS` = '2' AND `PARENT_ID` = ".$db->qstr($row['SYMBOLIC_ID']));
            $row['COUNT'] += $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `STATUS` = '2' AND `CATEGORY_ID` = ".$db->qstr($row['SYMBOLIC_ID']));
         }
         else
         {
            $row['COUNT'] = $db->GetOne("SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `STATUS` = '2' AND `PARENT_ID` = ".$db->qstr($row['ID']));
            $row['COUNT'] += $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `STATUS` = '2' AND `CATEGORY_ID` = ".$db->qstr($row['ID']));
         }

			$categs[] = $row;
		}

		$rs->Close();
	}
	if ($id > 0)
   {
		$db->Execute("UPDATE `{$tables['category']['name']}` SET `HITS` = `HITS` + 1 WHERE `ID` = ".$db->qstr($id));
   }
}

// Paging 4
$tpl->assign('list_total', $count);
// End Paging 4

$tpl->assign('category', $path[count($path) - 1]);
$tpl->assign('path', $path);
$tpl->assign('links', $links);
$tpl->assign('categs', $categs);

/* Top level Categories */
$topcats = $db->GetAll("SELECT * FROM `{$tables['category']['name']}` WHERE `STATUS` = 2 AND `PARENT_ID` = 0 ORDER BY `TITLE`");
$tpl->assign('topcats', $topcats);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('main.tpl', $id);
?>