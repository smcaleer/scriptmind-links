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

@ header ('Expires: Mon, 14 Oct 2002 05:00:00 GMT');              // Date in the past
@ header ('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT'); // always modified
@ header ('Cache-Control: no-store, no-cache, must-revalidate');  // HTTP 1.1
@ header ('Cache-Control: post-check=0, pre-check=0', false);
@ header ('Pragma: no-cache');                                    // HTTP 1.0

session_start ();
$_SESSION = array ();
if (isset ($_COOKIE[session_name ()]))
   setcookie (session_name (), '', time () - 42000, '/');

@ session_write_close ();
@ session_unset ();
@ session_destroy ();//Fix IE Bug

@ header ('Location: login.php');
exit();
?>