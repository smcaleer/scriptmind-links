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

// Disable any caching by the browser
@ header ('Expires: Mon, 14 Oct 2002 05:00:00 GMT');               // Date in the past
@ header ('Last-Modified: ' . gmdate ("D, d M Y H:i:s") . ' GMT'); // Always modified
@ header ('Cache-Control: no-store, no-cache, must-revalidate');   // HTTP 1.1
@ header ('Cache-Control: post-check=0, pre-check=0', false);
@ header ('Pragma: no-cache');                                     // HTTP 1.0

if (empty ($_POST['user']))
{
   SmartyValidate :: connect($tpl);
   SmartyValidate :: register_form('login', true);
   SmartyValidate :: register_validator('v_user', 'user', 'notEmpty', false, false, 'trim', 'login');
   SmartyValidate :: register_validator('v_pass', 'pass', 'notEmpty', false, false, 'trim', 'login');
}
else
{
   SmartyValidate :: connect($tpl);
   if (SmartyValidate :: is_valid($_POST, 'login'))
   {
      $sql = "SELECT `ID`, `NAME`, `ADMIN` FROM `{$tables['user']['name']}` WHERE `LOGIN` = ".$db->qstr($_POST['user'])." AND `PASSWORD` = ".$db->qstr(encrypt_password($_POST['pass']));

      $row = $db->GetRow($sql);
      if (!empty ($row['ID']))
      {
         // get permissions for this editor
         if ($row['ADMIN'] != 1)
         {
            $user_permission             = "";
            $user_grant_permission       = "";
            $user_permission_array       = array ();
            $user_grant_permission_array = array ();
            get_editor_permission($row['ID']);
            $_SESSION['user_permission']             = $user_permission;
            $_SESSION['user_grant_permission']       = $user_grant_permission;
            $_SESSION['user_permission_array']       = $user_permission_array;
            $_SESSION['user_grant_permission_array'] = $user_grant_permission_array;
         }
         if ($row['ADMIN'] || count($user_permission_array) > 0)
         {
            $_SESSION['user_id']   = $row['ID'];
            $_SESSION['is_admin']  = (($row['ADMIN'] == 1) ? 1 : 0);

            SmartyValidate :: disconnect();

            if (!preg_match ('`(admin|install)/(.*)\.php$`', $_SESSION['return']))
               unset ($_SESSION['return']);

            if ($_SESSION['return'])
            {
               @ header ("Location: ".$_SESSION['return']);
               unset ($_SESSION['return']);
            }
            else
               @ header ("Location: index.php");

            exit ();
         }
         else
            $tpl->assign('no_permission', true);
      }
      else
         $tpl->assign('failed', true);
   }
}

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('admin/login.tpl');
?>