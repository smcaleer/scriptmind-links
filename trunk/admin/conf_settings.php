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

$_REQUEST['c'] = (!empty ($_REQUEST['c']) && $_REQUEST['c'] > 0 ? intval ($_REQUEST['c']) : 1);

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

	SmartyValidate :: connect($tpl);
	SmartyValidate :: register_form('conf_settings', true);

   foreach ($conf as $i => $row)
   {
      if ($row['TYPE'] == 'STR')
         SmartyValidate :: register_validator('v_'.$row['ID'], $row['ID'], 'notEmpty', !$row['REQUIRED'], false, 'trim', 'conf_settings');
      elseif ($row['TYPE'] == 'PAS')
         SmartyValidate :: register_validator('v_'.$row['ID'], $row['ID'], 'notEmpty', !$row['REQUIRED'], false, 'trim', 'conf_settings');
      elseif ($row['TYPE'] == 'LOG')
         SmartyValidate :: register_validator('v_'.$row['ID'], $row['ID'], 'notEmpty', !$row['REQUIRED'], false, 'trim', 'conf_settings');
      elseif ($row['TYPE'] == 'URL')
         SmartyValidate :: register_validator('v_'.$row['ID'], $row['ID'], 'isURL'   , !$row['REQUIRED'], false, 'trim', 'conf_settings');
      elseif ($row['TYPE'] == 'EML')
         SmartyValidate :: register_validator('v_'.$row['ID'], $row['ID'], 'isEmail' , !$row['REQUIRED'], false, 'trim', 'conf_settings');
      elseif ($row['TYPE'] == 'INT')
         SmartyValidate :: register_validator('v_'.$row['ID'], $row['ID'], 'isInt'   , !$row['REQUIRED'], false, 'trim', 'conf_settings');
      elseif ($row['TYPE'] == 'NUM')
         SmartyValidate :: register_validator('v_'.$row['ID'], $row['ID'], 'isNumber', !$row['REQUIRED'], false, 'trim', 'conf_settings');
      elseif ($row['TYPE'] == 'LKP')
         SmartyValidate :: register_validator('v_'.$row['ID'], $row['ID'], 'notEmpty', !$row['REQUIRED'], false, 'trim', 'conf_settings');
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
	if (SmartyValidate :: is_valid($_REQUEST, 'conf_settings'))
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
      $conf[$i]['OPTIONS'] = array ('0' => _L('<Select an option>')) + $rs->GetAssoc();
   }
}

$tpl->assign('conf', $conf);
$tpl->assign('conf_categs', $conf_categs);

$content = $tpl->fetch('admin/conf_settings.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('admin/main.tpl');

?>