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

if (!isset ($_REQUEST['submit']))
{
	$VALIDATE_LINKS = 1;
	$VALIDATE_RECPR = 1;
	$IL = array (1,2);
	$AL = array ();
	if (RECPR_NOFOLLOW>0)
   {
		$IR = array ();
	}
   else
   {
		$IR = array (1,2);
	}
	$AR = array ();

}
else
{
	$VALIDATE_LINKS = $_REQUEST['VALIDATE_LINKS'];
	$VALIDATE_RECPR = $_REQUEST['VALIDATE_RECPR'];
	$IL = (isset ($_REQUEST['IL']) ? $_REQUEST['IL'] : array ());
	$AL = (isset ($_REQUEST['AL']) ? $_REQUEST['AL'] : array ());
	$IR = (isset ($_REQUEST['IR']) ? $_REQUEST['IR'] : array ());
	$AR = (isset ($_REQUEST['AR']) ? $_REQUEST['AR'] : array ());
	if ($VALIDATE_LINKS == 0 && $VALIDATE_RECPR == 0)
   {
		$tpl->assign('error', 1);
	}
   else
   {
		$start = 1;
		$tpl->assign('start', 1);
	}
}
$tpl->assign('VALIDATE_LINKS', $VALIDATE_LINKS);
$tpl->assign('VALIDATE_RECPR', $VALIDATE_RECPR);

if (!$start)
{
	$tpl->assign('IL', $IL);
	$tpl->assign('AL', $AL);
	$tpl->assign('IR', $IR);
	$tpl->assign('AR', $AR);
	$stat_inactive = array(1 => _L('Pending'), 2 => _L('Active'));
	$stat_active   = array(0 => _L('Inactive'), 1 => _L('Pending'));
	$tpl->assign('stat_inactive', $stat_inactive);
	$tpl->assign('stat_active', $stat_active);
	$categs = get_categs_tree(0);
	$categs[0] = '[All]';
	$tpl->assign('categs', $categs);
	$content = $tpl->fetch('admin/dir_validate.tpl');
	$tpl->assign('content', $content);
	echo $tpl->fetch('admin/main.tpl');
}
else
{
	set_time_limit(0);
	$tpl->assign('valid', array (0 => _L('Broken'), 1 => _L('Unknown'), 2 => _L('Valid'),));
	$columns = array ('URL' => _L('URL'));
	if ($VALIDATE_LINKS)
   {
		$columns['VALID'] = _L('Link Valid');
	}
	if ($VALIDATE_RECPR)
   {
		$columns['RECPR_VALID'] = _L('Recpr. Valid');
	}
	if ($VALIDATE_LINKS)
   {
		$columns['RESPONSE'] = _L('Link Response');
	}
	if ($VALIDATE_RECPR)
   {
		$columns['RECPR_RESPONSE'] = _L('Recpr. Response');
	}
	$tpl->assign('columns', $columns);
	$content = $tpl->fetch('admin/dir_validate.tpl');
	$tpl->assign('content', $content);
	$page = $tpl->fetch('admin/main.tpl');
	$page = split('<!--Progressbar-->', $page);

	echo $page[0];
	flush ();
	if ($_REQUEST['CATEGORY_ID'] > 0)
   {
		$where = " WHERE CATEGORY_ID = '".$_REQUEST['CATEGORY_ID']."'";
	}
	$rs = $db->Execute("SELECT `ID`, `URL`, `RECPR_URL`, `STATUS`, `ID`, `RECPR_REQUIRED` FROM `{$tables['link']['name']}` {$where}");
	$list = $rs->GetAssoc(true);
	$loopsize = count($list);
	$percent_per_loop = 100 / $loopsize;
	$percent_last = 0;
	$i = 1;
	foreach ($list as $id => $val)
   {
		if ($VALIDATE_LINKS)
      {
			list ($valid, $errstr) = validate_link($val['URL']);
			$data = array();
			$data['ID'] = $id;
			$val['VALID'] = $data['VALID'] = $valid;
			$data['LAST_CHECKED'] = gmdate('Y-m-d H:i:s');
			if($valid == 0 && ((in_array (1, $IL) && $val['STATUS'] == 1) ||  (in_array (2, $IL) && $val['STATUS'] == 2)))
         {
				$data['STATUS'] = 0;
			}
			if($valid == 2 && ((in_array (0, $AL) && $val['STATUS'] == 0) ||  (in_array (1, $AL) && $val['STATUS'] == 1)))
         {
				$data['STATUS'] = 2;
			}
			$db->Replace($tables['link']['name'], $data, 'ID', true);

			$tpl->assign('link_valid', $valid);
			$tpl->assign('errstr', $errstr);
		}

		if ($VALIDATE_RECPR)
      {
			$recpr_valid = check_recpr_link($val);
			$data = array();
			$data['ID'] = $id;
			$data['RECPR_VALID'] = ($recpr_valid > 0 ? 2 : 0);
			$data['RECPR_LAST_CHECKED'] = gmdate ('Y-m-d H:i:s');
			if ($val['RECPR_REQUIRED'] && $recpr_valid < 1 && ((in_array (1, $IR) && $val['STATUS'] == 1) || (in_array (2, $IR) && $val['STATUS'] == 2)))
         {
				$data['STATUS'] = 0;
			}
			if ($val['VALID']>0 && $recpr_valid >0 && ((in_array(0, $AR) && $val['STATUS'] == 0) || (in_array(1, $AR) && $val['STATUS'] == 1)))
         {
				$data['STATUS'] = 2;
			}
			$db->Replace($tables['link']['name'], $data, 'ID', true);

			$tpl->assign('recpr_valid', $val['RECPR_URL'] ? $recpr_valid : - 2);
		}

		// Progress bar update BEGIN
		$percent_now = round($i * $percent_per_loop);
		$difference = $percent_now - $percent_last;
		$tpl->assign('percent_last', $percent_last);
		$tpl->assign('difference', $difference);
		$tpl->assign('url', $val['URL']);
		$tpl->assign('recpr_url', $val['RECPR_URL']);
		$tpl->assign('row', $i);
		$percent_last = $percent_now;
		echo $tpl->fetch('admin/dir_validate_prog.tpl');
		flush();
		// Progress bar update END
		$i++;
	}
	echo $page[1];
	flush();
}
?>