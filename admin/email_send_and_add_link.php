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

$script_root = substr ($_SERVER["SCRIPT_NAME"], 0, strrpos ($_SERVER["SCRIPT_NAME"], '/'));
define ('DOC_ROOT', substr ($script_root, 0, strrpos ($script_root, '/')));

if (empty ($_POST['submit']) && !empty ($_SERVER['HTTP_REFERER']))
   $_SESSION['return'] = $_SERVER['HTTP_REFERER'];

if (empty ($_REQUEST['submit']))
{
   SmartyValidate :: disconnect();
   SmartyValidate :: connect($tpl, true);
   SmartyValidate :: register_criteria('checkEmail', 'check_email');
   SmartyValidate :: register_criteria('isNotEqual', 'validate_not_equal');
   SmartyValidate :: register_validator('v_TITLE'      , 'TITLE'        , 'notEmpty'  , false, false, 'trim');
   SmartyValidate :: register_validator('v_URL'        , 'URL'          , 'isURL'     , false, false, 'trim');
   SmartyValidate :: register_validator('v_EMAIL'      , 'EMAIL'        , 'isEmail'   , false, false, 'trim');
   SmartyValidate :: register_validator('v_check_email', 'EMAIL'        , 'checkEmail', false, false,  null );
   SmartyValidate :: register_validator('v_CATEGORY_ID', 'CATEGORY_ID:0', 'isNotEqual', true , false, 'trim');
}
else
{
   SmartyValidate :: connect($tpl);
   $data              = get_table_data('email');
   $data['DATE_SENT'] = gmdate ('Y-m-d H:i:s');

   if (strlen (trim ($data['URL'])) > 0 && !preg_match ('#^http[s]?:\/\/#i', $data['URL']))
      $data['URL'] = "http://".$data['URL'];

   $full_data = $data;
   $full_data['CATEGORY_ID'] = $_REQUEST['CATEGORY_ID'];
   $full_data['DESCRIPTION'] = $_REQUEST['DESCRIPTION'];

   if (SmartyValidate :: is_valid($full_data))
   {
      // Generate Link ID first
      $link_id = $db->GenID($tables['link']['name'].'_SEQ');
      $email_data = $full_data;
      $email_data['ADD_RECIPROCAL_URL'] = "http://" . $_SERVER['HTTP_HOST'] . DIRECTORY_ROOT . "/add_reciprocal.php?id=" . $link_id;

      $tmpl = $db->GetRow("SELECT `SUBJECT`, `BODY` FROM `{$tables['email_tpl']['name']}` WHERE `ID` = ".$db->qstr($_REQUEST['EMAIL_TPL_ID']));
      $mail = get_emailer();
      $mail->Body = replace_email_vars($tmpl['BODY'], $email_data);;
      $mail->Subject = replace_email_vars($tmpl['SUBJECT'], $email_data);
      $mail->AddAddress($email_data['EMAIL'], $email_data['NAME']);
      if (!DEMO)
         $sent = $mail->Send();
      else
         $sent = true;

      if ($sent)
      {
         $id = $db->GenID($tables['email']['name'].'_SEQ');
         $data['ID'] = $id;
         if ($db->Replace($tables['email']['name'], $data, 'ID', true) > 0)
         {
            // Save to Links table
            $link_data                   = get_table_data('link');
            $link_data['RECPR_REQUIRED'] = REQUIRE_RECIPROCAL;
            $link_data['STATUS']         = 2;
            $link_data['OWNER_NAME']     = $data['NAME'];
            $link_data['OWNER_EMAIL']    = $data['EMAIL'];
            $link_data['DATE_ADDED']     = gmdate('Y-m-d H:i:s');
            $link_data['DATE_MODIFIED']  = gmdate('Y-m-d H:i:s');
            if (ENABLE_PAGERANK)
            {
               require_once 'include/pagerank.php';
               $link_data['PAGERANK'] = get_page_rank($link_data['URL']);
            }
            $link_data['ID'] = $link_id;
            if ($db->Replace($tables['link']['name'], $link_data, 'ID', true) > 0)
            {
               $category = $db->GetOne("SELECT `TITLE` FROM `{$tables['category']['name']}` WHERE `STATUS` = '2' AND `ID` = ".$db->qstr($full_data['CATEGORY_ID']));
               $full_data['CATEGORY'] = $category;
               $tpl->assign('posted', true);
               $tpl->assign('sent', $full_data);
               $data = array();
            }
            else
               $tpl->assign('sql_error', $db->ErrorMsg());
         }
         else
            $tpl->assign('sql_error', $db->ErrorMsg());
      }
      else
         $tpl->assign('send_error', true);
   }
}

$rs = $db->Execute("SELECT `ID`, `TITLE` FROM `{$tables['email_tpl']['name']}` WHERE `TPL_TYPE` = '3'");
$tpls = $rs->GetAssoc();
$tpl->assign('tpls', $tpls);
$tpl->assign($full_data);
$tpl->assign('EMAIL_TPL_ID', $_REQUEST['EMAIL_TPL_ID']);
$tpl->assign('IGNORE'      , $_REQUEST['IGNORE']);

$categs = get_categs_tree(0);
$tpl->assign('categs', $categs);
$content = $tpl->fetch('admin/email_send_and_add_link.tpl');

$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('admin/main.tpl');

function check_email($value, $empty, & $params, & $form) {
   global $db, $tpl, $tables;
   $rs = $db->Execute("SELECT `ID`, `TITLE`, `URL` FROM `{$tables['link']['name']}` WHERE `URL` = ".$db->qstr($form['URL'])." OR `TITLE` = ".$db->qstr($form['TITLE']));
   $err['dir'] = array();
   while (!$rs->EOF)
   {
      if(strcasecmp($rs->Fields('URL'),$form['URL']) == 0)
      {
         $err['dir'][] = 'URL';
      }
      if(strcasecmp($rs->Fields('TITLE'),$form['TITLE']) == 0)
      {
         $err['dir'][] = 'TITLE';
      }
      $rs->MoveNext();
   }
   $rs = $db->Execute("SELECT * FROM `{$tables['email']['name']}` WHERE `URL` = ".$db->qstr($form['URL'])." OR `TITLE` = ".$db->qstr($form['TITLE'])." OR `EMAIL` = ".$db->qstr($form['EMAIL']));
   $err['email'] = array();
   while (!$rs->EOF)
   {
      $row = array('EMAIL'     => htmlentities (format_email($rs->Fields('EMAIL'), $rs->Fields('NAME'))),
                   'TITLE'     => $rs->Fields('TITLE'),
                   'URL'       => $rs->Fields('URL'),
                   'DATE'      => $rs->Fields('DATE_SENT'));
      if(strcasecmp($rs->Fields('EMAIL'),$form['EMAIL']) == 0) {
         $row['TYPE'] = 'EMAIL';
      }
      if(strcasecmp($rs->Fields('URL'),$form['URL']) == 0) {
         $row['TYPE'] = 'URL';
      }
      if(strcasecmp($rs->Fields('TITLE'),$form['TITLE']) == 0) {
         $row['TYPE'] = 'TITLE';
      }
      $err['email'][] = $row;
      $rs->MoveNext();
   }
   if(count($err['dir']) > 0 || count($err['email']) > 0)
   {
      $tpl->assign('email_send_errors', $err);
      return $_REQUEST['IGNORE'];
   }
   return 1;
}
?>