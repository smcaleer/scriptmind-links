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
   $data = '';
   $rs = $db->Execute("SELECT * FROM `{$tables['email']['name']}`");
   while(!$rs->EOF)
   {
      $data .= sprintf("%s\t%s\t%s\n", $rs->Fields('TITLE'), $rs->Fields('URL'), $rs->Fields('EMAIL'));
      $rs->MoveNext();
   }
   $length = strlen ($data);

   @ header("Content-type: application/force-download");
   @ header("Content-Disposition: attachment; filename=email.csv");
   @ header("Accept-Ranges: bytes");
   @ header("Content-Length: {$length}");
   echo $data;
}
else
{
   $content = $tpl->fetch('email_export.tpl');
   $tpl->assign('content', $content);

   //Clean whitespace
   $tpl->loadFilter('output', 'trimwhitespace');

   //Make output
   echo $tpl->fetch('main.tpl');
}
?>