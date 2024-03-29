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
#                 Portions copyright 2012-2013 Bruce Clement (http://www.clement.co.nz/)
# @projectManager David DuVal <david@david-duval.com>
# @package        PHPLinkDirectory
# ######################################################################
*/

error_reporting (E_ALL ^ E_WARNING ^ E_NOTICE);

try {
    error_reporting (E_ALL ^ E_WARNING ^ E_NOTICE);
    if( ! include_once '../config/config.php' ) {
        define('CONFIG_EXISTS',FALSE);
    } else {
        define('CONFIG_EXISTS',defined('INSTALL_PATH'));
    }
} catch (Exception $e) {
    define('CONFIG_EXISTS',FALSE);
}

if( ! CONFIG_EXISTS ) {
    // Configuration file doesn't exist
    // Need to add in the minimal config
    //  Add our installation path to the include_path
    define ('INSTALL_PATH', substr (__file__, 0, -17));

    if(!defined ('PATH_SEPARATOR'))
        define ('PATH_SEPARATOR', strtoupper (substr (PHP_OS, 0, 3)) == 'WIN' ? ';' : ':');

    ini_set ('include_path', ini_get ('include_path').PATH_SEPARATOR .INSTALL_PATH);

    define('TABLE_PREFIX','PLD_');
    define('ADODB_ASSOC_CASE', 1);
    require_once '../include/tables.php';
    define( 'USE_INTSMARTY', 1 );
    define('INSTALL_COMPLETE', 'NO' );
    define('DEMO', false);
}

require_once 'include/functions.php';
require_once 'libs/plugins/plugins.inc';
require_once 'install/config.php';
if( defined( 'USE_INTSMARTY' ))
    require_once 'libs/intsmarty/intsmarty.class.php';
require_once 'libs/smarty/SmartyValidate.class.php';
require_once 'include/version.php';
require_once 'include/data_upgrade.php';

$fn = INSTALL_PATH.'temp/templates';
if (!is_writable ($fn))
   @ chmod ($fn, 0777);

if (!is_writable ($fn))
   exit ( "<strong>The installer cannot start.</strong>\n
         <br />
         Please make sure that the folder <strong>".$fn."</strong> is writeable by the user the webserver runs under.");

session_start();

$step       = (!empty ($_REQUEST['step']) && preg_match ('`^[\d]+$`', $_REQUEST['step']) ? intval ($_REQUEST['step']) : 1);
$step       = ($step < 1 || $step > 5 ? 1 : $step); //Do not allow more/less steps than default

if( INSTALL_COMPLETE == 'YES' && $step < 5)
   exit ( "<strong>The site is already installed.</strong>\n
         <br />
         If you need to re-install, edit config/config.php and change the define for INSTALL_COMPLETE to 'NO'.");

$language   = (!empty ($_SESSION['language']) ? trim ($_SESSION['language']) : 'en');
$clear_all  = 0;

if (!is_dir ('../templates') || !is_dir ('../templates/install'))
   exit ( "<strong>The installer cannot find it's template files!</strong>\n
         <br />
         Please make sure that the folders <strong>templates/</strong> and <strong>templates/install/</strong> are available and readable by the user the webserver runs under.");

if( defined('USE_INTSMARTY' ) )
    $tpl = new IntSmarty($language);
else
    $tpl = new mySmarty();
$tpl->template_dir   = '../templates';
$tpl->compile_dir    = '../temp/templates';
$tpl->cache_dir      = '../temp/cache';
$tpl->compile_check  = false;

$path                   = request_uri();
$path_parts             = pathinfo ($path);
$path_parts['dirname']  = preg_replace ('`/install[\.]*`i', '', $path_parts['dirname']);

define ('DOC_ROOT', $path_parts['dirname']);

define ('TEMPLATE_PATH'     , 'templates/admin');
define ('FULL_TEMPLATE_PATH', DOC_ROOT.'/templates/admin');

if (!$_SESSION['nologin'] && !isset ($_SESSION['user_id']) && defined ('DB_DRIVER') && defined ('DB_HOST'))
{
   $db = ADONewConnection(DB_DRIVER);
   if($db->Connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME))
   {
      $check = $db->GetOne("SELECT COUNT(*) FROM `{$tables['user']['name']}` WHERE `LEVEL` = '1' AND `ACTIVE` = '1'");
      if (!empty ($check))
      {
         $_SESSION['return'] = DOC_ROOT.'/install/index.php';
         @ header('Location: '.DOC_ROOT.'/admin/login.php');
         @ exit ();
      }
      else
         $_SESSION['nologin'] = true;
   }
}
else
   $_SESSION['nologin'] = true;

switch ($step)
{
   case 1 :
      $tpl->assign('languages', select_lang());
      $tpl->assign('btn_next', 1);
      $tpl->assign('title', _L('Select Language'));

      if (empty ($_POST['submit']))
      {
         //Clear the entire cache
         $tpl->clear_all_cache();

         //Clear all compiled template files
         $tpl->clear_compiled_tpl();

         SmartyValidate :: connect($tpl, true);
         SmartyValidate :: register_form('install', true);
         SmartyValidate :: register_validator('v_language', 'language', 'dummyValid', false, false, 'trim', 'install');
      }
      else
      {
         SmartyValidate :: connect($tpl);
            if (SmartyValidate :: is_valid($_POST, 'install'))
            {
               $_SESSION['language'] = (!empty ($_POST['language']) ? $_POST['language'] : 'en');
               SmartyValidate :: disconnect();
               $step++;
               @ header('Location: index.php?step='.$step);
               @ exit ();
            }
            else
               $tpl->assign($_POST);
      }

      break;
   case 2 :
      $tpl->assign('req', check_requirements());
      $tpl->assign('btn_next', 1);
      $tpl->assign('btn_back', 1);
      $tpl->assign('title', _L('Welcome'));

      if (!empty ($_POST['submit']) && $_POST['submit'] == 'next')
      {
         SmartyValidate :: disconnect();
         $step++;
         @ header('Location: index.php?step='.$step);
         @ exit ();
      }
      elseif (!empty ($_POST['submit']) && $_POST['submit'] == 'back')
      {
         SmartyValidate :: disconnect();
         $step--;
         @ header('Location: index.php?step='.$step);
         @ exit ();
      }

      break;
   case 3 :

      $tpl->assign('btn_next', 1);
      $tpl->assign('btn_back', 1);
      $tpl->assign('title', _L('Database Settings'));

      if (empty ($_POST['submit']))
      {
         $_SESSION['values']                 = array ('db_driver' => 'mysql');
         $_SESSION['values']['db_driver']    = (defined ('DB_DRIVER')   ? DB_DRIVER   : 'mysql');
         $_SESSION['values']['db_host']      = (defined ('DB_HOST')     ? DB_HOST     : '');
         $_SESSION['values']['db_name']      = (defined ('DB_NAME')     ? DB_NAME     : '');
         $_SESSION['values']['db_user']      = (defined ('DB_USER')     ? DB_USER     : '');
         $_SESSION['values']['db_password']  = (defined ('DB_PASSWORD') ? DB_PASSWORD : '');

         SmartyValidate :: connect($tpl, true);
         SmartyValidate :: register_form('install', true);

         SmartyValidate :: register_validator('v_db_host'    , 'db_host'    , 'notEmpty'  , false, false, 'trim', 'install');
         SmartyValidate :: register_validator('v_db_name'    , 'db_name'    , 'notEmpty'  , false, false, 'trim', 'install');
         SmartyValidate :: register_validator('v_db_user'    , 'db_user'    , 'notEmpty'  , false, false, 'trim', 'install');
         SmartyValidate :: register_validator('v_db_password', 'db_password', 'dummyValid', true , false, 'trim', 'install');
      }
      else
      {
         if ($_POST['submit'] == 'next')
         {
            SmartyValidate :: connect($tpl);
            if (SmartyValidate :: is_valid($_POST, 'install'))
            {
               $db_details = array ();
               $db_details['db_driver']   = ('mysql');
               $db_details['db_host']     = (!empty ($_POST['db_host'])     ? $_POST['db_host']     : '');
               $db_details['db_name']     = (!empty ($_POST['db_name'])     ? $_POST['db_name']     : '');
               $db_details['db_user']     = (!empty ($_POST['db_user'])     ? $_POST['db_user']     : '');
               $db_details['db_password'] = (!empty ($_POST['db_password']) ? $_POST['db_password'] : null);
               $db_details['language']    = (!empty ($_SESSION['language']) ? $_SESSION['language'] : 'en');
               if (install_db($db_details))
               {
                  upgrade_user_table($db_details);
                  SmartyValidate :: disconnect();
                  $step++;
                  @ header('Location: index.php?step='.$step);
                  @ exit ();
               }
            }
         }
         elseif ($_POST['submit'] == 'back')
         {
            SmartyValidate :: disconnect();
            $step--;
            @ header('Location: index.php?step='.$step);
            @ exit ();
         }
      }

      $tpl->assign($_SESSION['values']);

      break;
   case 4 :
      $tpl->assign('btn_next', 1);
      $tpl->assign('btn_back', 1);
      $tpl->assign('title', _L('Administrative User'));

      if (empty ($_POST['submit']))
      {
         $db = ADONewConnection(DB_DRIVER);
         if ($db->Connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME))
         {
            $sql = "SELECT `LOGIN`, `NAME`, `EMAIL` FROM `{$tables['user']['name']}` WHERE `ADMIN` = '1'";
            $admin_update = $db->GetRow($sql);

            if (empty ($_SESSION['values']) || !is_array ($_SESSION['values']))
               $_SESSION['values']             = array ();
            $_SESSION['values']['admin_user']  = (!empty ($admin_update['LOGIN']) ? $admin_update['LOGIN'] : '');
            $_SESSION['values']['admin_name']  = (!empty ($admin_update['NAME'])  ? $admin_update['NAME']  : '');
            $_SESSION['values']['admin_email'] = (!empty ($admin_update['EMAIL']) ? $admin_update['EMAIL'] : '');
         }
         SmartyValidate :: connect($tpl, true);
         SmartyValidate :: register_form('install', true);

         SmartyValidate :: register_validator('v_admin_user'     , 'admin_user:!^\w{4,25}$!'       , 'isRegExp'  , false, false, 'trim', 'install');
         SmartyValidate :: register_validator('v_admin_name'     , 'admin_name'                    , 'notEmpty'  , false, false, 'trim', 'install');
         SmartyValidate :: register_validator('v_admin_password' , 'admin_password:6:25'           , 'isLength'  , false, false, 'trim', 'install');
         SmartyValidate :: register_validator('v_admin_passwordc', 'admin_password:admin_passwordc', 'isEqual'   , true , false, 'trim', 'install');
         SmartyValidate :: register_validator('v_admin_email'    , 'admin_email'                   , 'isEmail'   , false, false, 'trim', 'install');
      }
      else
      {
         if ($_POST['submit'] == 'next')
         {
            SmartyValidate :: connect($tpl);
            if (SmartyValidate :: is_valid($_POST, 'install'))
            {
               $admin_details = array ();
               $admin_details['admin_user']     = $_POST['admin_user'];
               $admin_details['admin_name']     = $_POST['admin_name'];
               $admin_details['admin_password'] = $_POST['admin_password'];
               $admin_details['admin_email']    = $_POST['admin_email'];

               if (create_admin($admin_details))
               {
                  SmartyValidate :: disconnect();
                  $step++;
                  @ header('Location: index.php?step='.$step);
                  @ exit ();
               }
            }
            elseif ($_POST['submit'] == 'back')
            {
               SmartyValidate :: disconnect();
               $step--;
               @ header('Location: index.php?step='.$step);
               @ exit ();
            }
         }
      }
         $tpl->assign($_SESSION['values']);

      break;
   case 5 :
      // Install finished.
      $ret = update_config('config/config.php', array ('INSTALL_COMPLETE' => 'YES' ));
      if ($ret === true || $ret == "CONFIG_NOT_WRITABLE" ) {
        // Make config files non-writable, if possible
        $fn = INSTALL_PATH.'config/config.php';
        @ chmod ($fn, 0444);
        $tpl->assign('config_file_writable',is_writable ($fn) ? 1 : 0 );

        $fn = INSTALL_PATH.'config';
        @ chmod ($fn, 0555);
        $tpl->assign('config_dir_writable',is_writable ($fn) ? 1 : 0 );
        $tpl->assign('title', _L('Installation Finished'));
      } else {
         $tpl->assign('title', _L('Installation Final Failed'));
         $tpl->assign('form_error', $ret);
      }

      $tpl->assign('btn_restart', 1);

      if ($_POST['submit'] == 'restart')
      {
         @ header('Location: index.php?step='.$step);
         @ exit ();
      }

      $clear_all = 1;
      break;
}

$tpl->assign($_POST);
$tpl->assign('language', $language);
$tpl->assign('errors'  , $errors);
$tpl->assign('messages', $messages);

$tpl->assign('VERSION' , CURRENT_VERSION);
$tpl->assign('step'    , $step);

//Clean whitespace
$tpl->loadFilter('output', 'trimwhitespace');

echo $tpl->fetch('install/main.tpl');

if ($clear_all == 1)
{
   //Clear the entire template cache
   $tpl->clear_all_cache();

   //Clear all compiled template files
   $tpl->clear_compiled_tpl();

   // Remove all stored information
   @ session_unset();
   @ session_destroy();
   if (isset ($_SESSION))
      unset ($_SESSION);
}

function check_requirements() {
   $requirements = array ();
   #PHP Vesion
   $result        = array ('req' =>_L('PHP Version &gt;= 5.2'));
   $result['ok']  = @ version_compare (@ phpversion(), '5.2', '>=');
   $result['txt'] = '('.@ phpversion().')';
   if (!$result['ok'])
      $result['txt'] .= _L('ScriptMind::Links may not work. Please upgrade!');

   $requirements[] = $result;

   #Server API
   $result       = array ('req' => _L('Server API'));
   $result['ok'] = php_sapi_name() != "cgi";
   if ($result['ok'])
      $result['txt'] = '('.php_sapi_name().')';
   else
      $result['txt'] = _L('CGI mode is likely to have problems.');

   $requirements[] = $result;

   #GD support
   $result       = array ('req' => _L('GD Support (for visual confirmations)'));
   $result['ok'] = extension_loaded ('gd');
   if ($result['ok'])
   {
      ob_start();
      @ phpinfo(8);
      $module_info = @ ob_get_contents();
      @ ob_end_clean();
      if (preg_match ("/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i", $module_info, $matches))
         $result['txt'] = '('.$matches[1].')';
      unset ($module_info, $matches);
   }
   else
      $result['txt'] = _L('Visual confirmation functionality will not be available.');

   $requirements[] = $result;

   #curl support
   $result       = array ('req' => _L('Curl Support (for checking submitted sites)'));
   $result['ok'] = extension_loaded ('curl');
   if ($result['ok'])
   {
      ob_start();
      @ phpinfo(8);
      $module_info = @ ob_get_contents();
      @ ob_end_clean();
      if (preg_match ("/\bcURL\s+Information\b[^\d\n\r]+?([\d\.]+)/i", $module_info, $matches))
         $result['txt'] = '('.$matches[1].')';
      unset ($module_info, $matches);
   }
   else
      $result['txt'] = _L('Checking of submitted sites and reciprocal links will not work.');

   $requirements[] = $result;

   #Session Save Path writable?
   $result = array ('req' => _L('Session Save Path writable?'));
   $sspath = @ ini_get ('session.save_path');
   if (preg_match ("`.+;(.*)`", $sspath, $matches))
   {
      $sspath = $matches[1];
      unset ($matches);
   }

   if (!$sspath)
   {
      $result['ok']  = false;
      $result['txt'] = _L('Warning: <span class="item">session.save_path ('.$sspath.')</span> is not set.');
   }
   elseif (is_dir ($sspath) && is_writable ($sspath))
   {
      $result['ok']  = true;
      $result['txt'] = '<span class="item">('.$sspath.')</span>';
   }
   else
   {
      $result['ok']  = false;
      $result['txt'] = _L('Warning: <span class="item">##sspath##</span> not existing or not writable.');
      $result['txt'] = str_replace ('##sspath##', $sspath, $r['txt']);
   }
   $requirements[] = $result;

   #MySQL Support
   $result       = array ('req' => _L('MySQL Support'));
   $result['ok'] = function_exists ('mysql_connect');
   if (!$result['ok'])
   {
      $result['txt']   = _L('Not available.');
      $result['fatal'] = true;
   }
   else
   {
      $mysql_version = @ mysql_get_server_info();
      if (empty ($mysql_version))
      {
         @ ob_start();
         @ phpinfo(8);
         $module_info = @ ob_get_contents();
         @ ob_end_clean();
         if (preg_match ("/\bClient\s+API\s+version\b[^\d\n\r]+?([\d\.]+)/i", $module_info, $matches))
            $mysql_version = $matches[1];
      }
      $result['txt']   = '('.(!empty ($mysql_version) ? trim ($mysql_version) : _L('Unknown MySQL server version')).')';
   }
   $requirements[] = $result;

   #./config/config.php writable?
   $fn = INSTALL_PATH.'config/config.php';
   if(file_exists ($fn) ) {
        $result = array ('req' => _L('./config/config.php writable?'));
        if (!is_writable ($fn))
           @ chmod ($fn, 0777);

        $result['ok'] = is_writable ($fn);
        if (!$result['ok'])
        {
           $result['txt']   = _L('Fatal: '.INSTALL_PATH.'config/config.php is not writable, installation cannot continue.');
           $result['fatal'] = true;
        }
   } else {
        $result = array ('req' => _L('./config writable?'));
        $fn = INSTALL_PATH.'config';
        if (!is_writable ($fn))
           @chmod($fn, 0777);

        $result['ok'] = is_writable ($fn);
        if (!$result['ok'])
        {
           $result['txt']   = _L('Fatal: '.INSTALL_PATH.'config is not writable, installation cannot continue.');
           $result['fatal'] = true;
        }
   }
   $requirements[] = $result;

   #./temp writable?
   $result = array ('req' => _L('./temp writable?'));
   $fn = INSTALL_PATH.'/temp';
   if (!is_writable ($fn))
      @chmod($fn, 0777);

   $result['ok'] = is_writable ($fn);
   if (!$result['ok'])
   {
      $result['txt']   = _L('Fatal: '.INSTALL_PATH.'temp is not writable, installation cannot continue.');
      $result['fatal'] = true;
   }
   $requirements[] = $result;

   #./temp/templates writable?
   $result = array ('req' => _L('./temp/templates writable?'));
   $fn = INSTALL_PATH.'temp/templates';
   if (!is_writable ($fn))
      @ chmod ($fn, 0777);

   $result['ok'] = is_writable ($fn);
   if (!$result['ok'])
   {
      $result['txt']   = _L('Fatal: '.INSTALL_PATH.'temp/templates is not writable, installation cannot continue.');
      $result['fatal'] = true;
   }
   $requirements[] = $result;

   return $requirements;
}

function upgrade_user_table($db_details) {
   global $tpl, $tables;

   $db = ADONewConnection($db_details['db_driver']);
   if($db->Connect($db_details['db_host'], $db_details['db_user'], $db_details['db_password'], $db_details['db_name']))
   {

      $user_data = $db->GetAll("SELECT * FROM `{$tables['user']['name']}`");
      $reg_date = gmdate ('Y-m-d H:i:s');

      foreach ($user_data as $user)
      {
         if (isset ($user['ADMIN']))
            $user['ADMIN'] = ($user['ADMIN'] == 1 ? 1 : 0);

         if (!preg_match ('`^(\{sha1\}|\{md5\})(.+)$`', $user['PASSWORD']))
            $user['PASSWORD'] = encrypt_password($user['PASSWORD']);

         $where = " `ID` = ".$db->qstr($user['ID']);
         if (!$db->AutoExecute($tables['user']['name'], $user, 'UPDATE', $where))
         {
            $tpl->assign('form_error', 'SQL_ERROR_ADMIN');
            $tpl->assign('sql_error', $db->ErrorMsg());

            return 0;
         }
      }

      unset ($user_data, $_SESSION['user_backup']);
      return 1;
   }
   else
      return 0;
}

function install_db($db_details) {
   global $tpl;

   if (!is_array ($db_details) || empty ($db_details))
   {
      $tpl->assign('form_error', _L('Could not process input data.'));

      return 0;
   }

   $ret = update_config('config/config.php', array ('LANGUAGE' => $db_details['language'], 'DB_DRIVER' => $db_details['db_driver'], 'DB_HOST' => $db_details['db_host'], 'DB_NAME' => $db_details['db_name'], 'DB_USER' => $db_details['db_user'], 'DB_PASSWORD' => $db_details['db_password']));
   if ($ret !== true)
   {
      $tpl->assign('form_error', $ret);
      return 0;
   }
   $ret = create_db(true, $db_details['db_driver'], $db_details['db_host'], $db_details['db_name'], $db_details['db_user'], $db_details['db_password']);
   if (!$ret[0])
   {
      // Database creation error
      $tpl->assign('form_error', $ret[1]);
      $tpl->assign('sql_error', $ret[2]);
      return 0;
   }
   else
   {
      // Database was created/updated
      $tpl->assign('message', $ret[1]);
      return 1;
   }
}

function create_admin($admin_details) {
   global $tpl, $db, $tables;

   $db = ADONewConnection(DB_DRIVER);
   if (!$db->Connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME))
      return false;

   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   $sql         = "SELECT * FROM `{$tables['user']['name']}` WHERE `LOGIN` = ".$db->qstr($admin_details['admin_user'])." LIMIT 1";
   $result      = $db->GetRow($sql);

   $max_user_id = $db->GetOne("SELECT MAX(`ID`) FROM `{$tables['user']['name']}`");
   $max_user_id = (empty ($max_user_id) ? 1 : $max_user_id + 1);

   $data                      = (!empty ($result) && is_array ($result) ? $result : get_table_data('user'));
   $data['LOGIN']             = $admin_details['admin_user'];
   $data['NAME']              = $admin_details['admin_name'];
   $data['PASSWORD']          = encrypt_password($admin_details['admin_password']);
   $data['EMAIL']             = $admin_details['admin_email'];
   $data['ADMIN']             = 1;
   $data['SUBMIT_NOTIF']      = ($data['SUBMIT_NOTIF']  == 0 ? 0 : 1);
   $data['PAYMENT_NOTIF']     = ($data['PAYMENT_NOTIF'] == 0 ? 0 : 1);

   if (empty ($result) || !is_array ($result))
   {
      $mode                   = "INSERT";
      $where                  = false;
      $data['ID']             = $db->GenID($tables['user']['name'].'_SEQ', $max_user_id);
   }
   else
   {
      $mode                   = "UPDATE";
      $where                  = " `ID` = ".$db->qstr($data['ID']);
      /* Create a new sequence to cater for upgrading installation */
      $db->CreateSequence($tables['user']['name'].'_SEQ', $max_user_id);
   }

   if (!$db->AutoExecute($tables['user']['name'], $data, $mode, $where))
   {
      $tpl->assign('form_error', 'SQL_ERROR_ADMIN');
      $tpl->assign('sql_error', $db->ErrorMsg());

      return false;
   }

   return 1;
}


function update_config($file_name, $values)
{
   $fullFileName=INSTALL_PATH.$file_name;
   if (!file_exists ($fullFileName)) {
      copy( INSTALL_PATH.'install/default_config.php', $fullFileName );
      if (!file_exists ($fullFileName))
         return 'CONFIG_NOT_FOUND';
      chmod($fullFileName,0666);
   }
   if (!is_writable ($fullFileName))
      return 'CONFIG_NOT_WRITABLE';
   $file = @ file_get_contents ($fullFileName);
   $vals = '';
   foreach ($values as $key => $val)
      if (!preg_match ("`define\s*\(\s*(?:'|\")$key(?:'|\")\s*,\s*(?:'|\")?.*(?:'|\")?\s*\);`Um", $file))
         $vals .= "define('$key', '$val');\n";
      else
         $file = preg_replace("`define\s*\(\s*(?:'|\")$key(?:'|\")\s*,\s*(?:'|\")?.*(?:'|\")?\s*\);`Um", "define('$key', '$val');", $file);

   if( $vals != '' ) {
        $insert_point = strrpos($file, '?>');
        if ($insert_point !== false)
           $file = substr ($file, 0, $insert_point).$vals.substr ($file, $insert_point);
        else
            $file=$file."\n".$vals;
   }

   $f = @ fopen ($fullFileName, 'w');
   @ fwrite ($f, $file);
   @ fclose ($f);
   return true;
}
