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

/**
 * Reads the configuration data from the database and sets it as constants
 * @author dcb
 */
function read_config($db)
{
   global $tables, $tpl;

   $sql = "SELECT * FROM `{$tables['config']['name']}`";
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   $rs = $db->Execute($sql);
   while (!$rs->EOF)
   {
      define ($rs->Fields('ID'), $rs->Fields('VALUE'));
      $rs->MoveNext();
   }
}

// Called late in the initialisation to set defaults for missing options
function set_defaults() {
    if( ! defined('TEMPLATE')) {
        if( IS_ADMIN ) {
            define( 'TEMPLATE', 'admin' );
        } else {
            define( 'TEMPLATE', 'GplLd' );
        }
        define('TEMPLATE','templates');
    }
    if( ! defined('TEMPLATE_BASE')) {
        $base=DOC_ROOT;
        if( IS_ADMIN ) {
            $base=substr( $base, 0, -6 );
        }
        define('TEMPLATE_BASE',$base.'/templates');
    }
    if( ! defined('TEMPLATE_DIR')) {
        if( TEMPLATE == '.' ) {
            define('TEMPLATE_DIR',TEMPLATE_BASE);
        } else {
            define('TEMPLATE_DIR',TEMPLATE_BASE.'/'.TEMPLATE);
        }
    }
    if( ! defined('ADMIN_TEMPLATE_DIR')) {
        define('ADMIN_TEMPLATE_DIR',TEMPLATE_BASE.'/admin');
    }
}

function gotoUnauthorized($reason, $tplFile, $returnVal=false)
{
   global $tpl;

   //Remove request variables
   unset ($_POST, $_GET, $_REQUEST);

   //Provide a reason why access was unautorised
   $tpl->assign('unauthorizedReason', $reason);

   //Clean whitespace
   $tpl->loadFilter('output', 'trimwhitespace');

   //Compress output for faster loading
   if (COMPRESS_OUTPUT == 1)
      $tpl->loadFilter('output', 'CompressOutput');

   $output = $tpl->fetch($tplFile);

   if ($returnVal)
   {
      return $output;
   }
   else
   {
      echo $output;
      exit();
   }
}
/**
 * Creates a Smarty object and sets default values
 * @author dcb
 */
require_once( "libs/smarty/SmartyBC.class.php");
require_once( "libs/smarty/plugins/block.l.php" );

class My_Security_Policy extends Smarty_Security {
  public $allow_php_tag = true;
  // allow PHP tags
  public $php_handling = Smarty::PHP_ALLOW;
  // allow everthing as modifier
  public $modifiers = array();
}
require_once( 'libs/smarty/SmartyBC.class.php');
class mySmarty extends SmartyBC {

    public function __construct()
    {
        parent::__construct();
        // enable security
        //$this->enableSecurity('My_Security_Policy');
        $this->registerPlugin('block', 'l', 'smarty_block_l');
        $this->php_handling=Smarty::PHP_ALLOW;
    }
    public function translate( $text )
    {
        return $text;
    }
}
function get_tpl() {
    // Temporarily disable intSmarty to allow smarty 3 upgrade
    if( defined('USE_INTSMARTY' ) )
        $tpl = new IntSmarty('en');
    else {
        $tpl = new mySmarty();
    }
    if( defined('TEMPLATE') && TEMPLATE != '.') {
        $tpl->template_dir = INSTALL_PATH.'templates/'.TEMPLATE;
    } else {
        $tpl->template_dir = INSTALL_PATH.'templates';
    }
	$tpl->compile_dir = INSTALL_PATH.'temp/templates';
	$tpl->cache_dir = INSTALL_PATH.'temp/cache';
	return $tpl;
}

/**
 * Shortcut function for string translations
 * @param string $str String to translate
 * @return string translated string
 * @author dcb
 */
function _L($str) {
	global $tpl;
	if (method_exists ($tpl, 'translate'))
		return $tpl->translate($str);
	else
		return $str;
}

/**
 * Extracts from the global variable $_REQUEST only the values those keys correspond to table column names
 *
 * @param string $table table name
 * @param bool $striptags strip html tags
 * @return array associative array
 * @author dcb
 *
 */
function get_table_data($table, $striptags = false) {
	global $tables;
	$data = array ();
    if( $striptags ) {
        foreach ($tables[$table]['fields'] as $col => $v) {
            if (isset ($_REQUEST[$col])) {
                $data[$col] = strip_tags($_REQUEST[$col]);
            }
        }

    } else {
        foreach ($tables[$table]['fields'] as $col => $v) {
            if (isset ($_REQUEST[$col])) {
                $data[$col] = $_REQUEST[$col];
            }
        }
    }
	return $data;
}

/**
 * Read language files, and get the language
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function select_lang($dirname = '../lang/')
{
   $lang  = array ();
   $files = array ();

   // Get language files of directory
   $extension = "php";
   $extension = str_replace (" ", "", $extension);
   $ext = explode (",", $extension);
   if ($handle = @ opendir ($dirname))
   {
      while (false !== ($file = readdir ($handle)))
         for ($i = 0; $i < sizeof ($ext);$i++)
            if (strstr ($file, ".".$ext[$i]) && stristr ($file, '~') === false && !empty ($ext[$i]))
               $files[] = $file;
      @ closedir ($handle);
   }

   //Select needed file info
   foreach ($files as $key => $f)
   {
      if (is_readable ($dirname.$f))
      {
         $lang_file_info = language_file_data($dirname.$f);
         $arr_key = substr ($f, 0, -4);
         if (isset ($lang_file_info['LANGUAGE']) && !empty ($lang_file_info['LANGUAGE']))
            $lang[$arr_key] = ucfirst ($lang_file_info['LANGUAGE']);
         else
            $lang[$arr_key] = ucfirst (substr ($f, 0, -4));
         unset ($f);
      }
   }

   unset ($file,$files, $dirname, $ext, $extension, $handle, $language, $i, $key, $arr_key, $f);
   natcasesort ($lang);

   return $lang;
}

/**
 * Get information of language filesize
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function language_file_data($lang_file)
{
   $lang_file = implode ('', file ($lang_file));

   preg_match ("|Language:(.*)|i"                , $lang_file, $lang       );
   preg_match ("|Language File Author:(.*)|i"    , $lang_file, $author_name);
   preg_match ("|Language File Author URL:(.*)|i", $lang_file, $author_URL );

   /* Remove unused vars and clean the information we get */
   unset ($lang_file,$lang[0],$author_name[0],$author_URL[0]);
   $lang[1]        = $lang[1];
   $author_name[1] = $author_name[1];
   $author_URL[1]  = $author_URL[1];

   return array ('LANGUAGE' => $lang[1], 'AUTHOR_NAME' => $author_name[1], 'AUTHOR_URL' => $author_URL[1]);
}

/**
 * Encode a password using "sha1" or "md5" depending on PHP version
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function encrypt_password($password='')
{
   if (empty($password))
      return false;

   if (version_compare (phpversion(), "4.3.0", ">=") && function_exists ('sha1'))
      return "{sha1}".sha1($password);
   else
      return "{md5}".md5($password);
}

function get_categs_tree($id=0)
{
	global $db, $tables;
	static $categs = array ("0" => "[Top]");
	static $level = 0;
	$level++;
	$rs = $db->Execute("SELECT `ID`, `TITLE` FROM `{$tables['category']['name']}` WHERE `STATUS` = '2' AND `PARENT_ID` = ".$db->qstr($id)." AND `SYMBOLIC` <> 1 ORDER BY `TITLE`");

	while (!$rs->EOF)
   {
		if (empty($_SESSION['user_id']) ||$_SESSION['is_admin'])
			$categs[$rs->Fields('ID')] = str_repeat('|&nbsp;&nbsp;&nbsp;', $level -1).'|___'.$rs->Fields('TITLE');
      else
      {
			if (in_array($rs->Fields('ID'),$_SESSION['user_permission_array']))
				$categs[$rs->Fields('ID')] = str_repeat('|&nbsp;&nbsp;&nbsp;', $level -1).'|___'.$rs->Fields('TITLE');
		}
		get_categs_tree($rs->Fields('ID'));
		$rs->MoveNext();
	}
	$level--;
	return $categs;
}

function get_grant_categs_tree($id=0)
{
   global $db, $tables;
   static $categs = array ("0" => "[Top]");
   static $level = 0;
   $level++;
   $rs = $db->Execute("SELECT `ID`, `TITLE` FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = ".$db->qstr($id)." AND `SYMBOLIC` <> 1 ORDER BY `TITLE`");

   while (!$rs->EOF)
   {
      if ($_SESSION['is_admin'])
         $categs[$rs->Fields('ID')] = str_repeat ('|&nbsp;&nbsp;&nbsp;', $level -1).'|___'.$rs->Fields('TITLE');
      else
      {
         if (in_array ($rs->Fields('ID'), $_SESSION['user_grant_permission_array']))
            $categs[$rs->Fields('ID')] = str_repeat ('|&nbsp;&nbsp;&nbsp;', $level -1).'|___'.$rs->Fields('TITLE');
      }

      get_grant_categs_tree($rs->Fields('ID'));
      $rs->MoveNext();
   }
   $level--;

   return $categs;
}

/**
 * Get visitor/client IP address
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function get_client_ip() {
   //Regular expression pattern for a valid IP address
   $ip_regexp = "/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/";

   //Retrieve IP address from which the user is viewing the current page
   if (isset ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]) && !empty ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]))
   {
      $visitorIP = (!empty ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"])) ? $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"] : ((!empty ($HTTP_ENV_VARS['HTTP_X_FORWARDED_FOR'])) ? $HTTP_ENV_VARS['HTTP_X_FORWARDED_FOR'] : @ getenv ('HTTP_X_FORWARDED_FOR'));
   }
   else
   {
      $visitorIP = (!empty ($HTTP_SERVER_VARS['REMOTE_ADDR'])) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : ((!empty ($HTTP_ENV_VARS['REMOTE_ADDR'])) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : @ getenv ('REMOTE_ADDR'));
   }

   //Clean IP address
   $visitorIP = preg_replace ($ip_regexp, "\\1.\\2.\\3.\\4", $visitorIP);

   return $visitorIP;
}

function get_page($list_total) {
	if (!isset ($_SESSION['p']))
   {
		$_SESSION['p'] = array ();
	}
	$page = isset ($_REQUEST['p']) ? $_REQUEST['p'] : (isset ($_SESSION[SCRIPT_NAME]['p']) ? $_SESSION[SCRIPT_NAME]['p'] : 1);
	if (($page -1) > ($list_total / LINKS_PER_PAGE))
   {
		$page = floor($list_total / LINKS_PER_PAGE) + 1;
	}
	$_SESSION[SCRIPT_NAME]['p'] = $page;
	$_REQUEST['p'] = $page;
	return $page;
}

function request_uri() {
	if ($_SERVER['REQUEST_URI'])
		return $_SERVER['REQUEST_URI'];

	//IIS with ISAPI_REWRITE
	if ($_SERVER['HTTP_X_REWRITE_URL'])
		return $_SERVER['HTTP_X_REWRITE_URL'];
	$p = $_SERVER['SCRIPT_NAME'];
	if ($_SERVER['QUERY_STRING'])
		$p .= '?'.$_SERVER['QUERY_STRING'];
	return $p;
}

function get_category($uri = NULL) {
	global $db, $tables;

	if ($uri != NULL)
   {
		$uri   = parse_url($uri);
		$query = $uri['query'];
		$path  = $uri['path'];
		parse_str($query, $vars);
		if (isset ($vars['c']))
			$cid = $vars['c'];
	}
   else
   {
		if (isset ($_REQUEST['c']))
			$cid = $_REQUEST['c'];
		$path = request_uri();
	}
	$id = 0;

	if (ENABLE_REWRITE && !isset ($cid))
   {
        if( substr( $path, 0, strlen(DOC_ROOT) ) == DOC_ROOT )
            $path = substr ($path, strlen(DOC_ROOT) + 1);
		$qp = strpos ($path, '?');
		if ($qp !== false)
      {
			$path = substr ($path, 0, $qp);
		}
		$path = explode ('/', $path);
		$id = 0;
		foreach ($path as $cat) {
			if (!empty ($cat)) {
				$id = $db->GetOne("SELECT `ID` FROM `{$tables['category']['name']}` WHERE `STATUS` = '2' AND `TITLE_URL` = ".$db->qstr($cat)." AND `PARENT_ID` = ".$db->qstr($id));
                if( is_null( $id ) )
                    $id = '0';
            }
		}
	}
	elseif (preg_match('`[\d]+`', $cid)) {
		$id = $db->GetOne("SELECT `ID` FROM `{$tables['category']['name']}` WHERE `STATUS` = '2' AND `ID` = ".$db->qstr($cid));
	}

	return ($id ? $id : '0');
}
function get_path($id) {
	global $db, $tables;
	$path = array ();
	$i = 0;
	while ($id != 0 && $i < 100)
   {
		$row = $db->GetRow("SELECT * FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($id));
		$id = $row['PARENT_ID'];
		$path[] = $row;
		$i ++;
	}
	$path[] = array ('ID' => '0', 'TITLE' => _L(SITE_NAME), 'TITLE_URL' => DOC_ROOT, 'DESCRIPTION' => SITE_DESC);
	return array_reverse($path);
}

function validate_link($url)
{
	$ret = get_url($url, URL_HEADERS);
	return array ($ret['status'] ? 2 : 0, ($ret['status'] || $ret['code']) ? $ret['response'] : $ret['error']);
}

function validate_captcha($value, $empty, &$params, &$form)
{
   require_once 'libs/captcha/captcha.class.php';

   return isset ($_SESSION['CAPTCHA']) && strtolower ($_SESSION['CAPTCHA']) == strtolower ($value);
}

/**
 * !! DEPRECATED !!
 * If you have updated from any prior version
 * please disable link ID feature from admin panel
 */
function init_submission()
{
   mt_srand();
   $_SESSION['RECPR_ID'] = mt_rand (0, 0xFFFFFF);
}

function validate_url_online($value, $empty, & $params, & $form)
{
	$ret = get_url($value, URL_HEADERS);
	return $ret['status'] ? 1 : 0;
}

function validate_recpr_link($value, $empty, &$params, &$form)
{
   global $tpl;

    if ($empty && empty ($value))
        return 1;

   $ret = check_recpr_link($form);
   if (empty ($ret) || $ret == 0 )
      return 0;

   return 1;
}

/**
 * Check that reciprocal link is from the submitted domain
 * @author Bruce Clement (http://www.clement.co.nz)
 * @license GPLv2 or later
 */
function validate_recpr_link_dom($value, $empty, &$params, &$form)
{
   if (empty ($value))
      return 1; // Will be checked later

    $Recip = parseDomain( $value );
    $Orig  = parseDomain( $form['URL'] );
    if( strcasecmp( $Orig, $Recip ) ) {
        return 0;

    }

    return 1;
}

/**
 * Check if reciprocal link page has a valid reciprocal link
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function check_recpr_link($data)
{
   $data['RECPR_URL'] = trim ($data['RECPR_URL']);

   if (empty ($data['RECPR_URL']))
      return -1;

   $ret = get_url($data['RECPR_URL'], URL_CONTENT);

   $RecipOrig = parseDomain( $ret['url_original'] );
   $RecipNow  = parseDomain( $ret['url'] );
   if( strcasecmp( $RecipNow, $RecipOrig ) ) {
       return -2;
   }


   $site_url = parseDomain(SITE_URL);
   if (!isset ($ret['content']) || empty ($ret['content'])) {
      return -1;
   } else {
       $dom=new DOMDocument();
       if( $dom->loadHTML($ret['content']) ) {
           /*** remove silly white space ***/
            $dom->preserveWhiteSpace = false;

            /*** get the links from the HTML ***/
            $links = $dom->getElementsByTagName('a');

            /*** loop over the links ***/
            foreach ($links as $tag)
            {
                $href=$tag->getAttribute('href');
                $test_url = parseDomain($href);
                if( $test_url == $site_url) {
                    $rel=$tag->getAttribute( 'rel');
                    if( empty( $rel ) || false===stripos($rel,'nofollow')) {
                        return true;
                    }
                }
            }
            return 0;
       } else {
            // malformed page
            //Look for reciprocal in any link tag
            $matches=array();
            preg_match_all ("`<(\s*)a([^>]*)".$site_url."([^>]*)>(.*)<\/a>`Ui", $ret['content'], $matches);

            //First check if matches array was created
            if (!is_array ($matches) || empty ($matches)) {
                return 0;
            }

            //Set internal pointer of array
            //to its first element
            reset ($matches);

            //Check if URL exists
            if (!is_array ($matches[key ($matches)]) || empty ($matches[key ($matches)])) {
                return 0;
            }

            //Check for nofollow
            foreach ($matches as $m => $match)
            {
                foreach ($match as $key => $value)
                {
                    //If anything nofollow is found validation fails
                    if (preg_match ('`rel[\s]*=[\s]*("|\')?[\s]*nofollow[\s]*("|\')`Ui', $value))
                    {
                    return 0;
                    }
                    unset ($match[$key]);
                }
                unset ($matches[$m]);
            }
            unset ($matches, $ret);
       }
   }
   return 1;
}

define ('URL_RESPONSE', 0);
define ('URL_HEADERS' , 1);
define ('URL_CONTENT' , 2);

/**
 * Fetch a URL, decode the result and return its components
 * @param string The URL to be parsed
 * @param int URL_HEADERS: Only fetch headers, URL_CONTENT: fetch headers and content
 * @param array Cookies to send with the request
 * @param string the user agent string to use
 * @return array the result of the fetch
 * @author Bruce Clement (http://www.clement.co.nz)
 * @license GPLv2 or later
 */
function get_url($url, $what = 0, $referer = "", $cookies = array (), $useragent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)")
{
  $curl = curl_init ( $url );
  curl_setopt ( $curl, CURLOPT_USERAGENT, $useragent );
  curl_setopt ( $curl, CURLOPT_FOLLOWLOCATION, true );
  curl_setopt ( $curl, CURLOPT_MAXREDIRS, 30 );
  curl_setopt ( $curl, CURLOPT_AUTOREFERER, true );
  curl_setopt ( $curl, CURLOPT_HEADER, true );
  if( $what == URL_HEADERS ) {
    curl_setopt ( $curl, CURLOPT_NOBODY, true );
  }
  if( ! empty( $cookies ) ) {
    $cookiestr='';
    foreach( $cookies as $key=>$value ) {
        $cookiestr .= ($cookiestr=='' ? '' : '; ') . $key.'='.$value;
    }
    curl_setopt ( $curl, CURLOPT_COOKIE, $cookiestr );
  }
  if( $referrer != '' )
    curl_setopt ( $curl, CURLOPT_REFERER, $referrer );
  curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );
  $answer = curl_exec( $curl );
  $headlen = curl_getinfo ( $curl, CURLINFO_HEADER_SIZE );
  $ret = curl_getinfo ( $curl );
  $ret['url_original'] = $url;
  if( $headlen >= strlen( $answer ) ) {
    $head = $answer;
    $ret['content'] = '';
  } else {
    $head = substr( $answer, 0, $headlen );
    $ret['content'] = substr( $answer, $headlen );
  }
  $headArray=preg_split( '/[\r\n]+/', $head );
  $resp = array_shift($headArray);
  $parts = array();
  if (preg_match ("`HTTP/1\.. (.*) (.*)`U", $resp, $parts))
  {
    $ret['status'] = $parts[1][0] == '2' || $parts[1][0] == '3';
    $ret['code'] = $parts[1];
  }
  $head = array();
  $cookies=array();
  $value=array();
  $pattern='/^([^=]*)=([^;]*);(.*)$/';
  foreach( $headArray as $line ) {
      if( $line === '' )
          continue;
      // Match "Date: Thu, 07 Jun 2012 09:13:58 GMT" -> array( 'Date', 'Thu, ... GMT'
      preg_match( '/^([^:]*):\s(.*)$/', $line, $value);
      if( $value[1] == 'Set-Cookie') {
          preg_match( $pattern, $value[2], $value );
          $cookies[ $value[1]] = $value[2];
      } else {
          $head[ $value[1]] = $value[2];
      }
  }
  $ret['headers'] = $head;
  $ret['cookies'] = $cookies;
  $ret['code'] = curl_getinfo ( $curl, CURLINFO_HTTP_CODE );
  curl_close( $curl );
  return $ret;
}

function parse_news($str)
{
    $start = stripos( $str, 'STARTNEWS--> </p>' );
    if( $start > 0 )
        $str = substr( $str, $start + 17);
    else {
        $start = stripos( $str, 'STARTNEWS-->' );
        if( $start > 0 )
            $str = substr( $str, $start + 12);
        else {
            $start = stripos( $str, '<body>' );
            if( $start > 0 )
                $str = substr( $str, $start + 6);
        }
    }

    $end = stripos( $str, '<!--ENDNEWS-->');
    if( $end > 0 )
        $str = substr( $str, 0, $end );
    else {
        $end = stripos( $str, '</body>');
        if( $end > 0 )
            $str = substr( $str, 0, $end );
    }
    $answer = array();
    $items = preg_split( '/<h2>/', $str, -1, PREG_SPLIT_NO_EMPTY );
    foreach( $items as $item ) {
        // Parse <a name="PHP_Version_Changed"></a>PHP Version Changed<a href="#PHP_Version_Changed" class="section_anchor"></a></h2><p>19 May 2012 ScriptMind::Links now uses ADOdb version 517 and as a result requires the PHP version to be at least 5.2 </p>
        $item = substr( $item, stripos(  $item, '</a>') + 4 );
        $split = stripos(  $item, '<a ');
        $title = substr( $item, 0, $split );
        $split = stripos(  $item, '</h2><p>' ) + 8;
        $item = substr( $item, $split );
        if( substr( $item, -4 ) == '</p>') {
           $item = substr( $item, 0, -4 );
        }
        preg_match('/(\d+)\s*(\w+)\s*(\d+)\s*(\S.*)$/', $item, $match);
        if(is_array($match) && count( $match ) > 4 ) {
            $answer[] = array (
                'title' => $title,
                'date' => $match[1] . " " . $match[2] . " " . $match[3],
                'body' => $match[ 4 ]
            );
        }
    }
    return $answer;
}

function check_allowed_feat($CategID=0)
{
   global $db, $tables;

   if (empty ($CategID))
      return 0;

   $count = $db->GetOne("SELECT COUNT(*) FROM `{$tables['link']['name']}` WHERE `STATUS` = '2' AND `FEATURED` = '1' AND `CATEGORY_ID` = ".$db->qstr($CategID));

   return ($count >= FTR_MAX_LINKS ? 0 : 1);
}

function validate_unique($value, $empty, &$params, &$form)
{
   return check_unique($params['field2'], $params['field'], $value, $params['field3'], $params['field4'], $form[$params['field4']], $params['field5'], $params['field6']);
}

/**
 * SmartyValidate function for URL unique test
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function validateUrlUnique($value, $empty, &$params, &$form)
{
   return checkUrlUnique($params['field2'], $params['field'], $value, $params['field3'], $params['field4'], $form[$params['field4']], $params['field5'], $params['field6']);
}

/**
 * Check if URL is unique
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function checkUrlUnique($table, $field, $value, $exclude_id = NULL, $parent_field = NULL, $parent_value = NULL, $exclude_from_field = NULL, $exclude_value = NULL)
{
   global $tables, $db;

   //Use only domain
   $value = parseDomain($value);

   if (empty ($value))
      return 0;

   $sql = "SELECT `URL` FROM `".$tables[$table]['name']."` WHERE `".$field."` LIKE ".$db->qstr('%'.$value.'%');
   if (strlen ($exclude_id) > 0)
      $sql .= " AND `ID` != ".$db->qstr($exclude_id);

   if (!empty ($parent_field))
      $sql .= " AND `".$parent_field."` = ".$db->qstr($parent_value);

   if (!empty ($exclude_from_field) && !empty ($exclude_value))
      $sql .= " AND `".$exclude_from_field."` != ".$db->qstr($exclude_value);

   //Retrieve simmilar URLs from DB
   $simmilarURLs = $db->GetCol($sql);

   if (!is_array ($simmilarURLs) || empty ($simmilarURLs))
   {
      //No simmilar URLs found
      return 1;
   }
   else
   {
      //Loop through each simmilar URL and compare
      foreach ($simmilarURLs as $key => $dbURL)
      {
         //Get only domain
         $dbURL = parseDomain($dbURL);

         //Check if domains match
         if (preg_match ('#^'.$value.'$#i', $dbURL))
         {
            //Domains matched
            return 0;
         }

         //Free some memory
         unset ($simmilarURLs[$key], $dbURL);
      }
   }

   return 1;
}

function check_unique($table, $field, $value, $exclude_id = NULL, $parent_field = NULL, $parent_value = NULL, $exclude_from_field = NULL, $exclude_value = NULL)
{
   global $tables, $db;
   $sql = "SELECT COUNT(*) FROM `{$tables[$table]['name']}` WHERE `{$field}` = ".$db->qstr($value);
   if (strlen ($exclude_id) > 0)
      $sql .= " AND `ID` != ".$db->qstr($exclude_id);

   if (!empty ($parent_field))
      $sql .= " AND `".$parent_field."` = ".$db->qstr($parent_value);

   if (!empty ($exclude_from_field) && !empty ($exclude_value))
      $sql .= " AND `".$exclude_from_field."` != ".$db->qstr($exclude_value);

   $c = $db->GetOne($sql);

   return ($c == 0 ? 1 : 0);
}

function validate_symbolic_unique($value, $empty, &$params, &$form)
{
   global $tpl, $tables, $db, $id;
   $sql = "SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `SYMBOLIC_ID` = ".$db->qstr($value)." AND `PARENT_ID` = " .$db->qstr($form['PARENT_ID']);
   if ($id > 0)
      $sql .= " AND ID <> ".$id;

   $c = $db->GetOne($sql);

   return ($c == 0 ? 1 : 0);
}

function validate_symbolic_parent($value, $empty, &$params, &$form)
{
   global $tpl, $tables, $db;
   $sql = "SELECT COUNT(*) FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($value)." AND `PARENT_ID` = ".$db->qstr($form['PARENT_ID']);
   $c = $db->GetOne($sql);

   return ($c == 0 ? 1 : 0);
}

function validate_not_equal($value, $empty, &$params, &$form)
{
   return $value != $params['field2'];
}

function validate_not_equal_var($value, $empty, &$params, &$form)
{
   return $value != $form[$params['field2']];
}

function parse_version($val)
{
    $start = strpos($val, '<tt>');
    if( $start > 0 ) {
        $val=  substr($val, $start+4);
        $end = strpos( $val, "</tt>");
        if( $end > 0 )
            $val = substr( $val, 0, $end );
        $val = '`'.$val.'`';
    } else if( FALSE===strpos($val, '`'))
        $val = '`'.$val.'`';
    preg_match('`(\d+)\.(\d+)\.(\d+)\s*((RC)(\d+))?`', $val, $match);
    $ver = sprintf("%02d%02d%02d%02d", $match[1], $match[2], $match[3], $match[6]);

	return $ver;
}

function format_version( $val ) {
    $rc = $val % 100;
    $val = ($val - $rc )/100;
    $rc = ( $rc > 0 ) ? "RC".$rc : "";
    $rev = $val % 100;
    $val = ($val - $rev )/100;
    $minor = $val % 100;
    $major = ($val - $minor )/100;
    $answer = sprintf( "%d.%d.%d%s", $major, $minor, $rev, $rc);
    return $answer;
}

if (!function_exists ('file_get_contents'))
{
   function file_get_contents ($fn)
   {
      $len = filesize ($fn);
      if(!$len)
         return false;

      $fp = @ fopen ($fn, 'r');
      if ($fp)
         return @ fread ($fp, $len);
      else
         return false;
   }
}

function set_log($file)
{
	@ ini_set ('display_errors', 0);
	@ ini_set ('log_errors', 1);
	@ ini_set ('error_log', INSTALL_PATH.'temp/'.$file);
	error_reporting (E_ALL ^ E_NOTICE);
}

function replace_email_vars($text, $data, $type = 1)
{
	if ($type == 1)
   {
		$prefix = 'EMAIL_';
	}
	elseif ($type == 2)
   {
		$prefix = 'LINK_';
	}
	elseif ($type == 3)
   {
		$prefix = 'PAYMENT_';
	}

	$text = str_replace('{MY_SITE_NAME}', SITE_NAME, $text);
	$text = str_replace('{MY_SITE_URL}' , SITE_URL , $text);
	$text = str_replace('{MY_SITE_DESC}', SITE_DESC, $text);

	foreach ($data as $k => $v)
   {
		$text = str_replace("{".$prefix.$k."}", $v, $text);
	}

	return trim ($text);
}

function get_emailer()
{
   global $db, $tables;

   if (!empty ($_SESSION['user_id']))
      $where = "`ID` = ".$db->qstr($_SESSION['user_id']);
   else
      $where = "`LEVEL` = '1'";

   $sql = "SELECT `NAME`, `EMAIL` FROM `".TABLE_PREFIX."USER` WHERE {$where} LIMIT 1";

   $user_info = $db->GetRow($sql);

   require_once 'libs/phpmailer/class.phpmailer.php';

   $mail            = new PHPMailer();
   $mail->PluginDir = 'libs/phpmailer/';
   $mail->From      = $user_info['EMAIL'];
   $mail->FromName  = $user_info['NAME'];
   $mail->Mailer    = EMAIL_METHOD;

   unset ($user_info, $sql);

   switch (EMAIL_METHOD)
   {
      case 'smtp' :
         $mail->Host = EMAIL_SERVER;
         if (strlen (EMAIL_USER) > 0)
         {
            $mail->SMTPAuth = true;
            $mail->Username = EMAIL_USER;
            $mail->Password = EMAIL_PASS;
         }
         break;
      case 'sendmail' :
         $mail->Sendmail = EMAIL_SENDMAIL;
         break;
   }
   return $mail;
}

function get_emailer_admin()
{
   global $tables, $db;

   $sql = "SELECT `NAME`, `EMAIL` FROM `".TABLE_PREFIX."USER` WHERE `ADMIN` = '1' LIMIT 1";

   $user_info = $db->GetRow($sql);

   require_once 'libs/phpmailer/class.phpmailer.php';

   $mail            = new PHPMailer();
   $mail->PluginDir = 'libs/phpmailer/';
   $mail->From      = $user_info['EMAIL'];
   $mail->FromName  = $user_info['NAME'];
   $mail->Sender    = $user_info['EMAIL'];
   $mail->Mailer    = EMAIL_METHOD;

   unset ($user_info, $sql);

   switch (EMAIL_METHOD)
   {
      case 'smtp':
         $mail->Host = EMAIL_SERVER;
         if (strlen (EMAIL_USER) > 0)
         {
            $mail->SMTPAuth = true;
            $mail->Username = EMAIL_USER;
            $mail->Password = EMAIL_PASS;
         }
         break;
      case 'sendmail':
         $mail->Sendmail = EMAIL_SENDMAIL;
         break;
   }
   return $mail;
}

function format_email($address, $name)
{
   $name    = trim ($name);
   $address = trim ($address);
   if ($name)
      return "{$name} <{$address}>";

   return $address;
}

/**
 * Check if installer and config file do not poses security risks
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function install_security_check()
{
   $warnings = array ();

   //Check if installer is still available
   $installer = 'install/index.php';
   if ( INSTALL_COMPLETE != 'YES' && is_file (INSTALL_PATH.$installer))
   {
      $installer_msg = _L('Installer is still available. This poses a major security risk, please remove ##INSTALLER## file immediately!');
      $warnings[]    = str_replace('##INSTALLER##', '<strong><em>'.trim ($installer).'</em></strong>', $installer_msg);
      unset ($installer_msg, $installer);
   }

   //Check if config file is still writeable
   $config_file = 'config/config.php';
   if (is_writable (INSTALL_PATH.$config_file))
      @ chmod ($config_file, 0755);

   if (is_writable (INSTALL_PATH.$config_file))
   {
      $config_msg = _L('Configuration file is still writeable by the user the webserver runs under. This poses a major security risk, please drop writing permissions for ##CONFIGFILE## file immediately!');
      $warnings[]    = str_replace('##CONFIGFILE##', '<strong><em>'.trim ($config_file).'</em></strong>', $config_msg);
      unset ($config_msg, $config_file);
   }

   return $warnings;
}

function db_replace($table, $data, $keyCol)
{
	global $tables, $db;

	foreach ($data as $key => $val)
   {
		if (substr($tables[$table]['fields'][$key], 0, 1) == 'T')
      {
			$data[$key] = $db->DBDate($val);
		}
      else
      {
			$data[$key] = $db->qstr($val);
		}
	}
	return $db->Replace($tables[$table]['name'], $data, $keyCol, false);
}

/**
 * Send submit/reject notifications
 * @param mixed $data if type is array it is considered a link associative array; otherwise it is considered a link id
 * @param boolean $update if change owner notification type
 * @author dcb
 */
function send_status_notifications($data, $update = true)
{
   global $db, $tables;
   if (DEMO)
      return;

   if (is_array ($data))
      $id = $data['ID'];
   else
   {
      $id = $data;
      $data = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($id));
   }

   if ($data['OWNER_NOTIF'] >= 2)
      return;
   if ($data['STATUS'] == 0)
      $tid = NTF_REJECT_TPL;
   elseif ($data['STATUS'] == 2)
      $tid = NTF_APPROVE_TPL;
   else
      return;


   $sql = "SELECT `SUBJECT`, `BODY` FROM `{$tables['email_tpl']['name']}` WHERE `ID` = ".$db->qstr($tid);
   $tmpl = $db->GetRow($sql);
   if ($tmpl)
   {
      $mail = get_emailer();
      $mail->Body    = replace_email_vars($tmpl['BODY']   , $data, 2);
      $mail->Subject = replace_email_vars($tmpl['SUBJECT'], $data, 2);
      if ($data['OWNER_EMAIL'])
      {
         $mail->AddAddress($data['OWNER_EMAIL'], $data['OWNER_NAME']);
         $sent = $mail->Send();
         if ($update)
            $db->Execute("UPDATE `{$tables['link']['name']}` SET `OWNER_NOTIF` = '2' WHERE `ID` = ".$db->qstr($id));
      }
   }
}

/**
 * Send submit notifications
 * @param mixed $data if type is array it is considered a link associative array; otherwise it is considered a link id
*/
function send_submit_notifications($data)
{
   global $db, $tables, $notif_msg;

   if (DEMO)
      return false;

   $sql = "SELECT `SUBJECT`, `BODY` FROM `{$tables['email_tpl']['name']}` WHERE `ID` = ".$db->qstr(NTF_SUBMIT_TPL);
   $tmpl = $db->GetRow($sql);

   if ($tmpl)
   {
      $mail          = get_emailer_admin();
      $mail->Body    = replace_email_vars($tmpl['BODY']   , $data, 2);
      $mail->Subject = replace_email_vars($tmpl['SUBJECT'], $data, 2);
      $mail->AddAddress($data['OWNER_EMAIL'], $data['OWNER_NAME']);
      $sent = $mail->Send();
   }

   $tmpl = $notif_msg['submit'];

   $admin = $db->GetRow("SELECT `ID`, `NAME`, `EMAIL` FROM `{$tables['user']['name']}` WHERE `ADMIN` = '1' AND `SUBMIT_NOTIF` = '1' LIMIT 1");
   $where = (!empty ($admin['ID']) ? 'AND `ID` != '.$db->qstr($admin['ID']) : '');

   $users = $db->GetAll("SELECT `NAME`, `EMAIL` FROM `{$tables['user']['name']}` WHERE `SUBMIT_NOTIF` = '1' {$where}");

   $mail          = get_emailer_admin();
   $mail->Body    = replace_email_vars($tmpl['BODY']   , $data, 2);
   $mail->Subject = replace_email_vars($tmpl['SUBJECT'], $data, 2);

   if (!empty ($admin['EMAIL']))
      $mail->AddAddress($admin['EMAIL'], $admin['NAME']);

   if (is_array ($users) && !empty ($users))
      foreach ($users as $user)
         $mail->AddBCC($user['EMAIL'], $user['NAME']);

   $sent = $mail->Send();
}

function send_payment_notifications($pdata, $ldata)
{
   global $db, $tables, $notif_msg;

   if (DEMO)
      return false;

   $pdata['SUCCESS'] = $pdata['CONFIRMED'] ? 'successful' : 'failed';

   $sql = "SELECT `SUBJECT`, `BODY` FROM `{$tables['email_tpl']['name']}` WHERE `ID` = ".$db->qstr(NTF_PAYMENT_TPL);
   $tmpl = $db->GetRow($sql);
   if ($tmpl)
   {
      $mail          = get_emailer_admin();
      $body          = replace_email_vars($tmpl['BODY']   , $ldata, 2);
      $subject       = replace_email_vars($tmpl['SUBJECT'], $ldata, 2);
      $mail->Body    = replace_email_vars($body           , $pdata, 3);
      $mail->Subject = replace_email_vars($subject        , $pdata, 3);
      $mail->AddAddress($data['OWNER_EMAIL'], $data['OWNER_NAME']);
      $sent = $mail->Send();
   }

   $tmpl = $notif_msg['payment'];

   $admin = $db->GetRow("SELECT `ID`, `NAME`, `EMAIL` FROM `{$tables['user']['name']}` WHERE `ADMIN` = '1' AND `SUBMIT_NOTIF` = '1' LIMIT 1");

   $where = (!empty ($admin['ID']) ? 'AND `ID` != '.$db->qstr($admin['ID']) : '');

   $users = $db->GetAll("SELECT `NAME`, `EMAIL` FROM `{$tables['user']['name']}` WHERE `PAYMENT_NOTIF` = '1' {$where}");

   $mail          = get_emailer();
   $body          = replace_email_vars($tmpl['BODY']   , $ldata, 2);
   $subject       = replace_email_vars($tmpl['SUBJECT'], $ldata, 2);
   $mail->Body    = replace_email_vars($body           , $pdata, 3);
   $mail->Subject = replace_email_vars($subject        , $pdata, 3);

   if (!empty ($admin['EMAIL']))
      $mail->AddAddress($admin['EMAIL'], $admin['NAME']);

   if (is_array ($users) && !empty ($users))
      foreach ($users as $user)
         $mail->AddBCC($user['EMAIL'], $user['NAME']);

   $sent = $mail->Send();
}

function add_date($timestamp, $months)
{
	$d = getdate($timestamp);
	$mon = $months % 12;
	$years = $months / 12;
	$mytime = mktime($d['hours'], $d['minutes'], $d['seconds'], $d['mon'] + $mon, 1, $d['year'] + $years);
	$days = min($d['mday'], date('t', $mytime));
	$mytime += ($days -1) * 86400;
	return $mytime;
}

function calculate_expiry_date($start, $units, $um)
{
	switch ($um) {
		case 1 :
			$mul = 1;
			break;
		case 2 :
			$mul = 3;
			break;
		case 3 :
			$mul = 6;
			break;
		case 4 :
			$mul = 12;
			break;
		default :
			$mul = 0;
			break;
	}
	if ($mul != 0)
   {
		return add_date($start, $units * $mul);
	}
	return 0;
}

function update_link_payment($pid, $data, $success, $raw)
{
   global $db, $tables;

   $pdata = $db->GetRow("SELECT * FROM `{$tables['payment']['name']}` WHERE `ID` = ".$db->qstr($pid));
   if (!$pdata['ID'])
      return false;

   $pdata['NAME']           = $data['name'];
   $pdata['EMAIL']          = $data['email'];
   $pdata['PAYED_TOTAL']    = (int) $data['total'];
   $pdata['PAYED_QUANTITY'] = (float) $data['quantity'];
   $pdata['CONFIRMED']      = ($success ? 1 : 0);
   $pdata['CONFIRM_DATE']   = gmdate ('Y-m-d H:i:s');
   $pdate['RAW_LOG']        = $raw;
  if ((float) $pdata['PAYED_TOTAL'] < (float) $pdata['TOTAL'])
   {
      $pdata['CONFIRMED'] = -1;
   }

   db_replace('payment', $pdata, 'ID');
   $ldata = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($pdata['LINK_ID']));
   send_payment_notifications($pdata, $ldata);
   //Take no action if link not found
   if (!$ldata['ID'])
      return false;

   $ldata['EXPIRY_DATE'] = '';
   if ($pdata['CONFIRMED'] != 1 || (float) $pdata['PAYED_TOTAL'] < (float) $pdata['TOTAL'])
      $ldata['PAYED'] = 0;
   else
   {
      $ldata['PAYED'] = $pdata['ID'];
      if (PAY_AUTO_ACCEPT)
      {
         $ldata['STATUS'] = 2;
         $exp_date = calculate_expiry_date(time(), $pdata['QUANTITY'], $pdata['UM']);
         if ($exp_date != 0)
            $ldata['EXPIRY_DATE'] = gmdate ('Y-m-d H:i:s', $exp_date);
      }
   }
   db_replace('link', $ldata, 'ID');
}

function sprint_r($val)
{
	ob_start();
	print_r($val);
	$ret = ob_get_contents();
	ob_end_clean();
	return $ret;
}

function numeric_entify_utf8($utf8_string)
{
	$out = "";
	$ns = strlen($utf8_string);
	for ($nn = 0; $nn < $ns; $nn ++) {
		$ch = $utf8_string[$nn];
		$ii = ord($ch);
		//1 7 0bbbbbbb (127)
		if ($ii < 128)
			$out .= $ch;
		//2 11 110bbbbb 10bbbbbb (2047)
		else
			if ($ii >> 5 == 6) {
				$b1 = ($ii & 31);
				$nn ++;
				$ch = $utf8_string[$nn];
				$ii = ord($ch);
				$b2 = ($ii & 63);
				$ii = ($b1 * 64) + $b2;
				$ent = sprintf("&#%d;", $ii);
				$out .= $ent;
			}
		//3 16 1110bbbb 10bbbbbb 10bbbbbb
		else
			if ($ii >> 4 == 14) {
				$b1 = ($ii & 31);
				$nn ++;
				$ch = $utf8_string[$nn];
				$ii = ord($ch);
				$b2 = ($ii & 63);
				$nn ++;
				$ch = $utf8_string[$nn];
				$ii = ord($ch);
				$b3 = ($ii & 63);
				$ii = ((($b1 * 64) + $b2) * 64) + $b3;
				$ent = sprintf("&#%d;", $ii);
				$out .= $ent;
			}
		//4 21 11110bbb 10bbbbbb 10bbbbbb 10bbbbbb
		else
			if ($ii >> 3 == 30) {
				$b1 = ($ii & 31);
				$nn ++;
				$ch = $utf8_string[$nn];
				$ii = ord($ch);
				$b2 = ($ii & 63);
				$nn ++;
				$ch = $utf8_string[$nn];
				$ii = ord($ch);
				$b3 = ($ii & 63);
				$nn ++;
				$ch = $utf8_string[$nn];
				$ii = ord($ch);
				$b4 = ($ii & 63);
				$ii = ((((($b1 * 64) + $b2) * 64) + $b3) * 64) + $b4;
				$ent = sprintf("&#%d;", $ii);
				$out .= $ent;
			}
	}
	return $out;
}

function xml_utf8_encode($str)
{
	return numeric_entify_utf8(htmlspecialchars ($str));
}

function validate_email_and_add_link($value, $empty, &$params, &$form)
{
   global $tpl, $tables, $db, $id;

   if ($form['TPL_TYPE'] !=3)
      return true;

   if(!empty ($id))
         return true;
   else
   {
      $sql = "SELECT `ID` FROM `{$tables['email_tpl']['name']}` WHERE `TPL_TYPE` = '3'";
      $c = $db->GetOne($sql);
      if ($c)
         return false;
      else
         return true;
   }
}

function validate_not_sub_category($value, $empty, &$params, &$form)
{
   global $tpl, $tables, $db, $u;

   $category = $value;

   $sql = "SELECT `PARENT_ID` FROM `{$tables['category']['name']}` WHERE `ID` = ".$form['CATEGORY_ID'];
   $category = $db->GetOne($sql);

   if ($category != 0)
   {
      $count_sql = "SELECT COUNT(*) FROM `{$tables['user_permission']['name']}` WHERE `USER_ID` = ".$db->qstr($u). " AND (`CATEGORY_ID` = ".$db->qstr($category);

      while ($category != 0)
      {
         $sql = "SELECT `PARENT_ID` FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($category);
         $category = $db->GetOne($sql);
         if ($category != 0)
            $count_sql .= " OR `CATEGORY_ID` = ".$db->qstr($category);
      }
      $count_sql .= ")";
      $c = $db->GetOne($count_sql);
   }
   else
      $c = 0;

   return ($c == 0 ? 1 : 0);
}


function find_child_categories()
{
   global $tables, $db, $data, $u;

   $child_count = 0;

   $rs = $db->Execute("SELECT `CATEGORY_ID` FROM `{$tables['user_permission']['name']}` WHERE `USER_ID` = ".$db->qstr($u));

   while (!$rs->EOF)
   {
      $row = $rs->FetchRow();

      $category = $row['CATEGORY_ID'];

      while ($category != 0)
      {
         $sql = "SELECT `PARENT_ID` FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($category);
         $category = $db->GetOne($sql);
         if ($category == $data['CATEGORY_ID'])
         {
            $child_count++;
            break;
         }
      }
   }
   $rs->Close();

   return $child_count;
}

function delete_child_categories()
{
   global $tables, $db, $id, $u;

   $child_count = 0;

   $rs = $db->Execute("SELECT `ID`, `CATEGORY_ID` FROM `{$tables['user_permission']['name']}` WHERE `USER_ID` = ".$db->qstr($u));

   while (!$rs->EOF)
   {
      $row = $rs->FetchRow();

      $category = $row['CATEGORY_ID'];

      while ($category != 0)
      {
         $sql = "SELECT `PARENT_ID` FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($category);
         $category = $db->GetOne($sql);
         if ($category == $id)
         {
            $db->Execute("DELETE FROM `{$tables['user_permission']['name']}` WHERE `ID` = ".$db->qstr($row['ID']));
            break;
         }
      }
   }
   $rs->Close();
}

function get_editor_permission($user_id=0)
{
   global $tables, $db, $user_grant_permission, $user_permission, $user_grant_permission_array, $user_permission_array;

   $all_first_iteration = true;
   $first_iteration     = true;

   $rs = $db->Execute("SELECT `CATEGORY_ID` FROM `{$tables['user_permission']['name']}` WHERE `USER_ID` = ".$db->qstr($user_id));

   while (!$rs->EOF)
   {
      $row = $rs->FetchRow();
      if ($all_first_iteration)
      {
         $user_permission = "ID = ".$db->qstr($row['CATEGORY_ID']);
         $all_first_iteration = false;
      }
      else
         $user_permission .= " OR ID = ".$db->qstr($row['CATEGORY_ID']);

      $user_permission_array[] = $row['CATEGORY_ID'];

      $new_sub_categories = get_sub_categories($row['CATEGORY_ID']);

      foreach ($new_sub_categories as $category_id)
      {
         if ($first_iteration)
         {
            $user_grant_permission = "ID = ".$db->qstr($category_id);
            $user_permission .= " OR ID = ".$db->qstr($category_id);
            $first_iteration = false;
         }
         else
         {
            $user_grant_permission .= " || ID = ".$db->qstr($category_id);
            $user_permission .= " OR ID = ".$db->qstr($category_id);
         }
         $user_permission_array[] = $category_id;
         $user_grant_permission_array[] = $category_id;
      }

   }
   $rs->Close();
}

function get_sub_categories($id=0)
{
   global $db, $tables;

   $categs = array ();
   $rs = $db->Execute("SELECT `ID`, `TITLE` FROM `{$tables['category']['name']}` WHERE `PARENT_ID` = ".$db->qstr($id)." ORDER BY `TITLE`");
   while (!$rs->EOF)
   {
      $categs[] = $rs->Fields('ID');
      $categs = array_merge ($categs, get_sub_categories($rs->Fields('ID')));
      $rs->MoveNext();
   }

   return $categs;
}

/**
 * Build URL rewrite path for a given category ID
 * @param  numeric category ID
 * @return Rewritten URL for category
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function construct_mod_rewrite_path($cat_id=0)
{
   global $tables, $db;

   $category        = $cat_id;
   $first_iteration = true;

   while ($category != 0)
   {
      //Get category title and add into URL
      $parent_row = $db->GetRow("SELECT `TITLE`, `TITLE_URL` FROM `{$tables['category']['name']}` WHERE `ID` = ". $db->qstr($category));
      if (empty ($parent_row['TITLE_URL']))
      {
         $parent_row['TITLE_URL'] = preg_replace ('`[^\w_-]`', '_', $parent_row['TITLE']);
         $parent_row['TITLE_URL'] = str_replace  ('__', '_', $parent_row['TITLE_URL']);
      }
      if($first_iteration)
      {
         $mod_rewrite_url = $parent_row['TITLE_URL'];
         $first_iteration = false;
      }
      else
         $mod_rewrite_url = $parent_row['TITLE_URL']."/".$mod_rewrite_url;

      $category = $db->GetOne("SELECT `PARENT_ID` FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($category));
   }

   $mod_rewrite_url = DOC_ROOT."/{$mod_rewrite_url}";

   return $mod_rewrite_url;
}

/**
 * Parse URL and return its components
 * @param string The URL to be parsed
 * @return array Submitted URL components
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function parseURL($url)
{
   $url = trim ($url);

   if (empty ($url))
      return false;

   return parse_url ($url);
}

/**
 * Parse URL and return its domain name
 * @param string  The URL to be parsed
 * @return string Domain name of URL
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function parseDomain($url)
{
   $url = trim ($url);

   if (empty ($url))
      return false;

   $output = parse_url($url);

   if (!isset ($output['host']))
      return false;

   $pattern = array ('`^http[s]?:`', '`^ftp:`', '`^mailto:`', '`^www.`', '`^\.`', '`\.$`', '`[^\w\d-\.]`');
   $output['host'] = preg_replace ($pattern, '', $output['host']);

   return $output['host'];
}

/**
 * [DEBUG] Print an array
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function print_array($array)
{
   echo "<div style=\"text-align:left;\"><pre>";
   print_r ($array);
   echo "</pre></div>";
}

/**
 * Clean/handle white-space chars or given ressource (array|string)
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 *
 * @param  mixed [array|string]
 * @return mixed [array|string]
 */
function filter_white_space($ressource)
{
   if (!empty ($ressource))
   {
      if (is_array ($ressource))
      {
         foreach ($ressource as $key => $value)
         {
            if (is_string ($value))
               $ressource[$key] = clean_str_white_space($value);
         }

         return $ressource;
      }
      elseif (is_string ($ressource))
      {
         $ressource = clean_str_white_space($ressource);
         return $ressource;
      }
   }
   else
   {
      return false;
   }
}

/**
 * Clean a string of unneeded white-space chars
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
function clean_str_white_space($string='')
{
   //Windows to *nix
   $string = str_replace ("\r\n"  , "\n"      , $string);
   //Mac to *nix
   $string = str_replace ("\r"    , "\n"      , $string);
   //TABs
   $string = str_replace ("\t"    , " "       , $string);
   //NULL BYTE
   $string = str_replace ("\0"    , ""        , $string);
   //Vertical TABs
   $string = str_replace ("\x0B"  , ""        , $string);
   //Multiple white-space chars
   //$string = preg_replace ('#[\s]+#i', ' '       , $string);
   $string = trim ($string);

   return $string;
}

/**
 * Clean a string of all white-space chars, except simple space only once
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 *
 * @param string Text to be processed
 * @param string String with which to replace white-space characters
 * @return string clean text
 */
function strip_white_space($string='', $replace=' ')
{
   //Remove all kind of white-space chars
   $search = array ( "\n",   //*NIX
                     "\r",   //Mac
                     "\r\n", //Windows
                     "\t",   //Tab
                     "\x0B", //Vertical Tab
                     "\0"    //NULL BYTE
                 );
   $string = str_replace ($search, '', $string);

   //Remove multiple white-spaces
   $string = preg_replace ('#[\s]+#i', $replace, $string);

   $string = trim ($string);
   return $string;
}

/**
 * Login a user. Originally in login.php
 * @author FrozenMinds.com and Bruce Clement (http://www.clement.co.nz)
 *
 * @param string user code
 * @param string user's password
 * @return boolean logon succeeded
 */
function login($user,$password,$admin_only=false)
{
    global $db,$tables;
    $sql = "SELECT `ID`, `NAME`, `ADMIN` FROM `{$tables['user']['name']}` WHERE `LOGIN` = ".$db->qstr($user)." AND `PASSWORD` = ".$db->qstr(encrypt_password($password));

    $row = $db->GetRow($sql);
    if (empty ($row['ID']))
        return false;

    // get permissions for this editor
    if ($row['ADMIN'] != 1)
    {
        if( $admin_only )
            return false;
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
       return true;
    }
    return false;
}

/**
 * Make an anchor class for plugins
 * @global type $db
 * @return \PluginAnchor
 */
function makePluginAnchor() {
    global $db;
    $anchor = new PluginAnchor();
    $anchor->tablePrefix = TABLE_PREFIX;
    $anchor->db = $db;
    return $anchor;
}
