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

error_reporting (E_ALL ^ E_WARNING ^ E_NOTICE);

@ header ('Content-Type: text/html; charset=utf-8');

/**
 * Add our installation path to the include_path
 */
define ('INSTALL_PATH', substr (__file__, 0, -18));

if(!defined ('PATH_SEPARATOR'))
   define ('PATH_SEPARATOR', strtoupper (substr (PHP_OS, 0, 3)) == 'WIN' ? ';' : ':');

ini_set ('include_path', ini_get ('include_path').PATH_SEPARATOR .INSTALL_PATH);

define('TABLE_PREFIX','PLD_');
define('ADODB_ASSOC_CASE', 1);
require_once 'include/tables.php';
define('DEMO', false);

?>