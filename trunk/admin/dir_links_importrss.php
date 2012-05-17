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
require_once 'include/rss_parser.php';

if (empty ($_REQUEST['submit']) && !empty ($_SERVER['HTTP_REFERER']))
   $_SESSION['return'] = $_SERVER['HTTP_REFERER'];

$cid = $_REQUEST['c'];
$tpl->assign('cid',$cid);
$tpl->assign('path',get_path($cid));

if (empty ($_POST['submit']))
{
   SmartyValidate :: disconnect();
   SmartyValidate :: connect($tpl, true);
   SmartyValidate :: register_validator('v_URL', 'rss_url', 'isURL', false, false, 'trim');
}
else
{
   SmartyValidate :: connect($tpl);
   if (strlen (trim ($_REQUEST['rss_url'])) > 0 && !preg_match ('#^http[s]?:\/\/#i', $_REQUEST['rss_url']))
      $_REQUEST['rss_url'] = "http://".$_REQUEST['rss_url'];

   $tpl->assign('rss_url', $_REQUEST['rss_url']);
   if (SmartyValidate :: is_valid($_REQUEST))
   {
      $rss        = new rssParser();
      $rss_result = $rss->parse($_REQUEST['rss_url']);
      if($rss_result !== true)
         $tpl->assign('error', $rss_result);

      $tpl->assign('link_count', count($rss->items));
      $links = array();
      if (count($rss->items) > 0)
      {
         foreach ($rss->items as $item)
            $links[] = add_link($cid,$item['link'], $item['title'], $item['description'], $_POST['status']);

         $tpl->assign('list', $links);
         $tpl->assign('columns', array ('TITLE' => _L('Title'), 'URL' => _L('URL'), 'ERROR' => _L('Result')));
      }
   }
}

$content = $tpl->fetch('admin/dir_links_importrss.tpl');
$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('admin/main.tpl');

// Function to add link data to database
function add_link($cid,$link, $title = 'N/A', $desc = 'N/A', $status = '2')
{
   global $db, $tables;
   $data                   = array ();
   $data['TITLE']          = $title;
   $data['DESCRIPTION']    = $desc;
   $data['CATEGORY_ID']    = $cid;
   $data['URL']            = $link;
   $data['RECPR_REQUIRED'] = 0;
   $data['STATUS']         = $status;
   $error                  = array ();
   if (!check_unique('link', 'TITLE', $title, NULL, 'CATEGORY_ID', $cid))
      $error['TITLE'] = true;

   if (ALLOW_MULTIPLE == 1)
      $cu = check_unique('link', 'URL', $link, NULL, 'CATEGORY_ID', $cid);
   else
      $cu = check_unique('link', 'URL', $link, NULL);

   if (!$cu)
      $error['URL'] = true;

   if (count ($error) > 0)
   {
      $data['ERROR'] = $error;
      return $data;
   }

   $data['IPADDRESS']      = $client_info['IP'];
   if (!empty ($client_info['HOSTNAME']))
      $data['DOMAIN']      = $client_info['HOSTNAME'];

   $data['VALID']         = 1;
   $data['LAST_CHECKED']  = gmdate('Y-m-d H:i:s');
   $data['DATE_ADDED']    = gmdate('Y-m-d H:i:s');
   $data['DATE_MODIFIED'] = gmdate('Y-m-d H:i:s');
   if (strlen (trim ($data['URL'])) > 0 && !preg_match ('#^http[s]?:\/\/#i', $data['URL']))
      $data['URL'] = "http://".$data['URL'];

   if (ENABLE_PAGERANK)
   {
      require_once '../include/pagerank.php';
      $data['PAGERANK'] = get_page_rank($data['URL']);
   }
   $id = $db->GenID($tables['link']['name'].'_SEQ');
   $data['ID'] = $id;

   if ($db->Replace($tables['link']['name'], $data, 'ID', true) == 0)
      $error['SQL'] = true;

   $data['ERROR'] = $error;
   return $data;
}
?>