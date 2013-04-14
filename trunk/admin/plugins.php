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
echo "<pre>";
print_r($_REQUEST);
print_r($plugins);
echo "</pre>";
exit();

if (empty ($_REQUEST['submit']))
{
	$sql = "SELECT `ID`, `VALUE` FROM `{$tables['config']['name']}`";
	$conf_vals = $db->GetAssoc($sql);

	foreach($conf as $k => $row)
   {
		if($conf[$k]['CONFIG_GROUP'] != $_REQUEST['c'])
      {
			unset($conf[$k]);
		}
      else
      {
			$conf[$k]['VALUE'] = $conf_vals[$row['ID']];
		}
	}
    $tpl->assign('opt_bool', array(1 => $tpl->translate('Yes'), 0 => $tpl->translate('No')));
	SmartyValidate :: connect($tpl);
	SmartyValidate :: register_form('plugins', true);

   foreach ($conf as $i => $row)
   {
      if ($row['TYPE'] == 'STR')
         SmartyValidate :: register_validator('v_'.$row['ID'], $row['ID'], 'notEmpty', !$row['REQUIRED'], false, 'trim', 'plugins');
      elseif ($row['TYPE'] == 'PAS')
         SmartyValidate :: register_validator('v_'.$row['ID'], $row['ID'], 'notEmpty', !$row['REQUIRED'], false, 'trim', 'plugins');
      elseif ($row['TYPE'] == 'LOG')
         SmartyValidate :: register_validator('v_'.$row['ID'], $row['ID'], 'notEmpty', !$row['REQUIRED'], false, 'trim', 'plugins');
      elseif ($row['TYPE'] == 'URL')
         SmartyValidate :: register_validator('v_'.$row['ID'], $row['ID'], 'isURL'   , !$row['REQUIRED'], false, 'trim', 'plugins');
      elseif ($row['TYPE'] == 'EML')
         SmartyValidate :: register_validator('v_'.$row['ID'], $row['ID'], 'isEmail' , !$row['REQUIRED'], false, 'trim', 'plugins');
      elseif ($row['TYPE'] == 'INT')
         SmartyValidate :: register_validator('v_'.$row['ID'], $row['ID'], 'isInt'   , !$row['REQUIRED'], false, 'trim', 'plugins');
      elseif ($row['TYPE'] == 'NUM')
         SmartyValidate :: register_validator('v_'.$row['ID'], $row['ID'], 'isNumber', !$row['REQUIRED'], false, 'trim', 'plugins');
      elseif ($row['TYPE'] == 'LKP')
         SmartyValidate :: register_validator('v_'.$row['ID'], $row['ID'], 'notEmpty', !$row['REQUIRED'], false, 'trim', 'plugins');
   }
}
else
{
	if (ENABLE_PAGERANK==0)
   {
		$_REQUEST['SHOW_PAGERANK'] = 0;
	}
	if ($_REQUEST['SHOW_PAGERANK']==0 && $_REQUEST['DEFAULT_SORT'] == 'P')
   {
		$_REQUEST['DEFAULT_SORT'] = 'H';
	}

   foreach ($conf as $i => $row)
   {
		if ($conf[$i]['CONFIG_GROUP'] != $_REQUEST['c'])
      {
			unset($conf[$i]);
		}
      else
      {
         $conf[$i]['VALUE'] = $_REQUEST[$row['ID']];
		}
	}

	SmartyValidate :: connect($tpl);
	if (SmartyValidate :: is_valid($_REQUEST, 'plugins'))
   {
      $posted = true;
      if (!DEMO)
      {
         $errors   = 0;
         $cust_msg = '';
         foreach ($conf as $row)
         {
            $posted = $db->AutoExecute($tables['config']['name'], $row, 'UPDATE', '`ID` = '.$db->qstr($row['ID']));
            if (!$posted)
            {
               break;
            }
         }
         $tpl->assign('posted', $posted);
		}
	}
}

foreach ($conf as $i => $val)
{
   if ($conf[$i]['TYPE']=='LKP' && is_string ($conf[$i]['OPTIONS']))
   {
      $rs = $db->Execute($conf[$i]['OPTIONS']);
      $conf[$i]['OPTIONS'] = array ('0' => _L('&lt;Select an option&gt;')) + $rs->GetAssoc();
   }
}

$tpl->assign('conf', $conf);
$tpl->assign('conf_categs', $conf_categs);

$content = $tpl->fetch('plugins.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->loadFilter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('main.tpl');
