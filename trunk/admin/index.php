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

//Clear the entire cache
$tpl->clear_all_cache();

//Clear all compiled template files
$tpl->clear_compiled_tpl();

$url = get_url('http://api.gplld.com/version/', URL_CONTENT, $_SERVER['SERVER_NAME'].request_uri());
$sv  = parse_version($url['content']);
$cv  = parse_version(CURRENT_VERSION);

//Version check
if ($sv > $cv)
{
   $version = _L('A new version (##VERSION##) is available.');
   $version = str_replace('##VERSION##', trim ($url['content']), $version);
   $tpl->assign('update_available', 1);
}
else
{
   $version = _L('Your installation is up to date, no updates are available for your version of gplLD.');
   $tpl->assign('update_available', 0);
}

//Security check
$security_warnings = install_security_check();
if (!empty ($security_warnings))
   $tpl->assign('security_warnings', $security_warnings);

unset ($security_warnings);

//Directory statistics
$stats[0] = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `STATUS` > '1'");
$stats[1] = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `STATUS` = '1'");
$stats[2] = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `STATUS` = '0'");
$stats[3] = $db->GetOne("SELECT COUNT(*) FROM `{$tables['category']['name']}`");
$stats[4] = $db->GetOne("SELECT COUNT(*) FROM `{$tables['email']['name']}`");
$stats[5] = $db->GetOne("SELECT COUNT(*) FROM `{$tables['email_tpl']['name']}`");

//phpLinkDirectory News
if (ENABLE_NEWS)
{
   $url = get_url("http://api.gplld.com/news/", URL_CONTENT);
   if ($url['status'])
   {
      $news = parse_news($url['content']);
      $tpl->assign('news', $news);
   }
}

$tpl->assign('stats', $stats);
$tpl->assign('version', $version);


$content = $tpl->fetch('admin/index.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('admin/main.tpl');
?>