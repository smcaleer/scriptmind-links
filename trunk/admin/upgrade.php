<?php
/**
# ######################################################################
# Project:     ScriptMind::Links: Version 0.2.0
# **********************************************************************
# Copyright (C) 2013 Bruce Clement. (http://www.clement.co.nz/)
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU Lesser General Public License
# as published by the Free Software Foundation; either version 3
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
# ScriptMind::Links Forum
#
# @link           http://www.scriptmind.org/
# @copyright      2013 Bruce Clement. (http://www.clement.co.nz/)
# @license http://URL GPLv2 or later
# @projectManager Bruce Clement
# @package        ScriptMind::Links
# ######################################################################
*/
define('DOING_UPGRADE', true);
require_once 'init.php';
require_once 'include/data_upgrade.php';

/* @var $db ADOConnection */

if( defined( 'CURRENT_SCHEMA' ) && (int)CURRENT_SCHEMA >= (int)REQUIRED_SCHEMA ) {
?><html><head><title>Upgrade complete</title></head>
<body><h1>Upgrade Complete</h1><p>Scriptmind::Links database is at the same
version as the installed copy of the software.</p>
<p><a href="index.php">Return</a>
to to index.</p></body></html><?php
exit;
}

echo "Updating database schema<br/>\n";
flush();
$create=create_db( false );
if( $create[0]) {
    echo "Result: Ok: ".$create[1]."<br/><br/>\n\n";
} else {
    echo "Result: Failed: ".$create[1]." ".$create[2]."<br/><br/>\n\n";
    echo "Either fix the problem or restore the database and package software.<br/>\n";
    exit;
}
if( ! defined('CURRENT_SCHEMA')) {
    define( 'CURRENT_SCHEMA', '0');
    $template=$tables['config']['data'][0];
    $row=array();
    foreach( $template as $fld => $val) {
        switch( $fld ) {
            case 'VALUE' :      $row['VALUE'] =  CURRENT_SCHEMA ; break;
            case 'ID'    :      $row['ID']    = 'CURRENT_SCHEMA'; break;
            default      :      $row[ $fld  ] = $val;             break;
        }
    }
    if( true !== $db->AutoExecute($tables['config']['name'], $row, 'INSERT', false, true, true) ) {
        echo $template;
    }
}
$updateLeft = "Update `{$tables['config']['name']}` set `VALUE`=";
$updateRight = " where `ID`='CURRENT_SCHEMA'";
for( $toSchema = 1+(int)CURRENT_SCHEMA; $toSchema <= (int)REQUIRED_SCHEMA; ++ $toSchema) {
    echo "Updating to logical schema version $toSchema<br/>\n";
    flush();
    switch( $toSchema ) {
        case 1 : break;
    }
    $db->Execute($updateLeft.$toSchema.$updateRight);
}
echo "<br/>\nFinished<br/><br/>\n\n";
echo "Database upgraded to version ".REQUIRED_SCHEMA."<br/>\n";
echo '<a href="index.php">Continue</a>';