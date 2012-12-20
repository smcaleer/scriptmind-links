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
#                 Portions copyright 2012 Bruce Clement (http://www.clement.co.nz/)
# @projectManager David DuVal <david@david-duval.com>
# @package        PHPLinkDirectory
# ######################################################################
*/
require_once 'include/client_info.php';
require_once 'include/version.php';
require_once 'config/config.php';
require_once 'include/tables.php';
require_once 'include/functions.php';

session_start();

$script_dir = substr ($_SERVER["SCRIPT_NAME"], 0, strrpos ($_SERVER["SCRIPT_NAME"], '/'));
$script_pos = strpos( $_SERVER['REQUEST_URI'], $script_dir);
if( $script_pos !== FALSE && $script_pos == 0  ) {
    define ('DOC_ROOT', $script_dir );
} else { // our script isn't in the path starting from server document root
    define ('DOC_ROOT', '');
}
unset( $script_dir );
unset( $script_pos );

if (!defined ('DB_DRIVER'))
{
   @ header('Location: '.DOC_ROOT.'/install/index.php');
   @ exit;
}

if( defined('USE_INTSMARTY' ) )
    require_once 'libs/intsmarty/intsmarty.class.php';
else {
    require_once 'libs/smarty/SmartyBC.class.php';
}
require_once 'libs/smarty/SmartyValidate.class.php';
require_once 'libs/adodb/adodb.inc.php';

if (get_magic_quotes_gpc())
{
   function stripslashes_deep($value)
   {
       $value = (is_array ($value) ? array_map ('stripslashes_deep', $value) : stripslashes ($value));
       return $value;
   }

   $_REQUEST = array_map ('stripslashes_deep', $_REQUEST);
   $_COOKIE  = array_map ('stripslashes_deep', $_COOKIE);
}

//Connect to database
$db = ADONewConnection(DB_DRIVER);
if($db->Connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME))
{
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   read_config($db);
}
else
   define('ERROR', 'ERROR_DB_CONNECT');

if (DEBUG === 1)
   set_log('frontend_log.txt');


//Initialize template
$tpl = get_tpl();
$tpl->cache_lifetime = 0;

$tpl->assign('VERSION', CURRENT_VERSION);

require_once 'include/constants.php';

$URLcomponents = @ parse_url ($_SERVER['REQUEST_URI']);
if (is_array ($URLcomponents) && !empty ($URLcomponents))
{
   @ parse_str ($URLcomponents['query'], $URLvariables);
}
?>