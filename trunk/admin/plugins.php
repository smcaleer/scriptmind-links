<?php
/**
# ######################################################################
# Project:     ScriptMind::Plugins: Version 0.0.1
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
# @license http://URL LGPLv3 or later
# @projectManager Bruce Clement
# @package        ScriptMind::Plugins
# ######################################################################
*/

require_once 'init.php';
if( ! empty ($_REQUEST['Install']) ) {
    $pluginName=substr( $_REQUEST['Install'], 8 );
    $plugin = Plugin::create($pluginName, $anchor);
    $plugin->Active = false;
    $plugin->save();
}
$deletedPlugins=array();
$keys = preg_grep( '/^Delete_/', array_keys( $_POST ) );
foreach( $keys as $key ) {
    $pluginName=(int)substr( $key, 7 );
    $deletedPlugins[$pluginName] = true;
    $plugins = Plugin::load($anchor, "ID=$pluginName", false);
    if( ! empty( $plugins) ) {
        $plugins[0]->delete();
    }
}
$plugins = Plugin::allAvailablePlugins($anchor);
$allPlugins=  $plugins[4];
$changedPlugins = array();
$activeChanged = false;
foreach( $_POST as $input => $value ) {
    $i = strrpos($input, "_");
    if( $i > 0 ) {
        $field = substr( $input, 0, $i);
        $pluginName = substr($input, $i+1);
        if( $pluginName > 0 && array_key_exists($pluginName, $allPlugins) ) {
            $plugin = $allPlugins[ $pluginName ];
            if( $plugin->{$field} != $value) {
                $plugin->set_option($field, $value);
                $changedPlugins[$pluginName] = $plugin;
                $activeChanged |= ( $field == 'Active');
            }
        }
    }
}
foreach ($changedPlugins as $plugin) {
    $plugin->save();
}
// If anything has had its Active flag changed it will move to a different list so we re-read
if( $activeChanged ) {
    $plugins = Plugin::allAvailablePlugins($anchor);
}

$tpl->assign('activePlugins', $plugins[0]);
$tpl->assign('inactivePlugins', $plugins[1]);
$tpl->assign('availablePlugins', $plugins[2]);
$tpl->assign('failedPlugins', $plugins[3]);

$tpl->assign('posted', $posted);

$content = $tpl->fetch('plugins.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->loadFilter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('main.tpl');
