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

if (empty ($_POST['submit']) && !empty ($_SERVER['HTTP_REFERER']))
   $_SESSION['return'] = $_SERVER['HTTP_REFERER'];

if (empty ($_POST['submit']))
{
   SmartyValidate :: disconnect();
   SmartyValidate :: connect($tpl, true);
   SmartyValidate :: register_criteria('checkEmail'    , 'check_email');
   SmartyValidate :: register_validator('v_TITLE'      , 'TITLE'      , 'notEmpty'  , false, false, 'trim');
   SmartyValidate :: register_validator('v_URL'        , 'URL'        , 'isURL'     , false, false, 'trim');
   SmartyValidate :: register_validator('v_EMAIL'      , 'EMAIL'      , 'isEmail'   , false, false, 'trim');
   SmartyValidate :: register_validator('v_check_email', 'EMAIL'      , 'checkEmail', false, false,  null );
}
else
{
   SmartyValidate :: connect($tpl);
   $data = get_table_data('email');
   $data['DATE_SENT'] = gmdate ('Y-m-d H:i:s');

   if (strlen (trim ($data['URL'])) > 0 && !preg_match ('#^http[s]?:\/\/#i', $data['URL']))
      $data['URL'] = "http://".$data['URL'];

   if (SmartyValidate :: is_valid($data))
   {
      $tmpl = $db->GetRow("SELECT `SUBJECT`, `BODY` FROM `{$tables['email_tpl']['name']}` WHERE `ID` = ".$db->qstr($_POST['EMAIL_TPL_ID']));
      $mail = get_emailer();
      $mail->Body    = replace_email_vars($tmpl['BODY'], $data);;
      $mail->Subject = replace_email_vars($tmpl['SUBJECT'], $data);
      $mail->AddAddress($data['EMAIL'], $data['NAME']);
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
            $tpl->assign('posted', true);
            $tpl->assign('sent', $data);
            $data = array();
         }
         else
            $tpl->assign('sql_error', $db->ErrorMsg());
      }
      else
         $tpl->assign('send_error', true);
   }
}
$rs   = $db->Execute("SELECT `ID`, `TITLE` FROM `{$tables['email_tpl']['name']}` WHERE `TPL_TYPE` = 1");
$tpls = $rs->GetAssoc();
$tpl->assign('tpls', $tpls);
$tpl->assign($data);
$tpl->assign('EMAIL_TPL_ID', $_POST['EMAIL_TPL_ID']);
$tpl->assign('IGNORE', $_POST['IGNORE']);
$content = $tpl->fetch('admin/email_send.tpl');

$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch('admin/main.tpl');

function check_email($value, $empty, &$params, &$form) {
   global $db, $tpl, $tables;

   $rs = $db->Execute("SELECT `ID`, `TITLE`, `URL` FROM `{$tables['link']['name']}` WHERE `URL` = ".$db->qstr($form['URL'])." OR `TITLE` = ".$db->qstr($form['TITLE']));
   $err['dir'] = array ();
   while (!$rs->EOF){
      if (strcasecmp ($rs->Fields('URL'),$form['URL'])==0)
         $err['dir'][] = 'URL';
      if (strcasecmp ($rs->Fields('TITLE'),$form['TITLE'])==0)
         $err['dir'][] = 'TITLE';

      $rs->MoveNext();
   }
   $rs = $db->Execute("SELECT * FROM `{$tables['email']['name']}` WHERE `URL` = ".$db->qstr($form['URL'])." OR `TITLE` = ".$db->qstr($form['TITLE'])." OR `EMAIL` = ".$db->qstr($form['EMAIL']));
   $err['email'] = array();
   while(!$rs->EOF) {
      $row = array ('EMAIL' => htmlentities (format_email($rs->Fields('EMAIL'), $rs->Fields('NAME'))),
                'TITLE' => $rs->Fields('TITLE'),
                'URL' => $rs->Fields('URL'),
                     'DATE' => $rs->Fields('DATE_SENT'));
      if (strcasecmp ($rs->Fields('EMAIL'), $form['EMAIL']) == 0)
         $row['TYPE'] = 'EMAIL';

      if (strcasecmp ($rs->Fields('URL'), $form['URL']) == 0)
         $row['TYPE'] = 'URL';

      if (strcasecmp ($rs->Fields('TITLE'), $form['TITLE']) ==0 )
         $row['TYPE'] = 'TITLE';

      $err['email'][] = $row;
      $rs->MoveNext();
   }
   if (count ($err['dir']) > 0 || count($err['email']) > 0) {
      $tpl->assign('email_send_errors', $err);
      return $_POST['IGNORE'];
   }
   return 1;
}
?>