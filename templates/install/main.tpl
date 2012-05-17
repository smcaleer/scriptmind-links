{include file="install/messages.tpl"}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <title>phpLinkDirectory v{$smarty.const.CURRENT_VERSION} Install - {$title}</title>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
   <!-- <link rel="stylesheet" type="text/css" href="install.css" /> -->
   	{* CSS Style file *}
   <link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/install/style.css" />
   
   <meta name="robots" content="noindex, nofollow" />
   <meta name="generator" content="PHP Link Directory {$VERSION}" />
   <script type="text/javascript" src="install.js"></script>
</head>
<body>
<div id="body-container">
<div id="header-container">
<div id="header">
<h1 id="site-title">gplLD Installer</h1>
<h2 id="site-description">{$title}</h2>
<div id="container">
<div id="content">

<form method="post" action="">
{$smarty.capture.form_error}
{$smarty.capture.message}

{* Step 1 *}
{if empty($step) or $step le 1}
<h2>Language</h2>
<table border="0" class="formPage" width="100%">
  <tr>
    <td width="30%">{l}Language{/l}</td>
    <td width="70%" class="smallDesc">
      {html_options options=$languages selected=$language name="language"}
      {validate form="install" id="v_language" page="1" message=$smarty.capture.field_required}
      {l}Select the language for the installation process.{/l}
      {l}The language used through the installation process.{/l}
   </td>
  </tr>
</table>

{* Step 2 *}
{elseif $step eq 2}

<h2>
	{l}Thank you for choosing gplLD. gplLD was developed to help create and maintain a link directory. Keep up-to-date by visiting the gplLD <ahref="http://www.gplld.com">homepage</a>.{/l}
</h2>
<h2>{l}Requirements{/l}</h2>
<table id="requirements" border="0" cellpadding="0" cellspacing="0">
{assign var="fatal" value=false}
   {foreach from=$req item="item"}
   <tr>
      <td>{$item.req}</td>
      <td> <img src="{$smarty.const.DOC_ROOT}/templates/install/images/{if $item.fatal}close_16.png{assign var="fatal" value=true}{elseif $item.ok}add_16.png{else}close_16.png{/if}" width="16" height="16" alt="" /> </td>
      <td><span>&nbsp;{$item.txt}</span></td>
   </tr>
   {/foreach}
</table>

{if $fatal}
<div class="warning">
   <img src="{$smarty.const.DOC_ROOT}/templates/install/images/close_16.png" alt="" />
   {l}At least one fatal error was found. Please correct the reported error(s) and refresh this page or restart the installer in order to continue with the installation process.{/l}
</div>
{/if}

{elseif $step eq 3}
<h2>{l}MySQL Database connection and login information.{/l}</h2>
<br />
<table border="0" class="formPage" width="100%">
  <tr>
    <td width="30%">{l}Database Server{/l}<span class='req'>*</span></td>
    <td width="70%" class="smallDesc">
      <input {if $errors.db_host}class="warning" {/if}type="text" name="db_host" value="{$db_host}" size="20" maxlength="100" />
      {validate form="install" id="v_db_host" page="3" message=$smarty.capture.field_required}
      <br />
      <p>{l}Hostame or IP-address of the database server{/l}</p>
      <p>{l}The database server can be in the form of a hostname, such as db1.myserver.com, or as an IP-address, such as 192.168.0.1{/l}</p>
   </td>
  </tr>
  <tr>
    <td width="30%">{l}Database Name{/l}<span class='req'>*</span></td>
    <td width="70%" class="smallDesc">
      <input type="text" name="db_name" value="{$db_name}" size="20" maxlength="100" />
      {validate form="install" id="v_db_name" page="3" message=$smarty.capture.field_required}
      <br />
      <p>{l}Database Name{/l}</p>
      <p>{l}The database used to hold the data. An example database name is 'phplinkd'.{/l}</p>
   </td>
  </tr>
  <tr>
    <td width="30%">{l}Username{/l}<span class='req'>*</span></td>
    <td width="70%" class="smallDesc">
      <input type="text" name="db_user" value="{$db_user}" size="20" maxlength="100" />
      {validate form="install" id="v_db_user" page="3" message=$smarty.capture.field_required}
      <br />
     <p>{l}Database username{/l}
      <p>{l}The username used to connect to the database server. An example username is 'mysql_10'.{/l}<em>{l}Note: Create and Drop permissions are required at this point of the installation procedure.{/l}</em></p>
   </td>
  </tr>
  <tr>
    <td width="30%">{l}Password{/l}</td>
    <td width="70%" class="smallDesc">
      <input type="password" name="db_password" value="{$db_password}" size="20" maxlength="100" />
      <br />
      <p>{l}Database password{/l}</p>
      <p>{l}The password is used together with the username, which forms the database user account.{/l}</p>
   </td>
  </tr>
</table>

{elseif $step eq 4}
<h2>{l}Create an administrative user for the phpLinkDirectory.{/l}</h2>
<br />
<table border="0" class="formPage" width="100%">
  <tr>
    <td width="30%">{l}Username{/l}<span class='req'>*</span></td>
    <td width="70%" class="smallDesc">
      <input type="text" name="admin_user" value="{$admin_user}" size="20" maxlength="25" />
      {validate form="install" id="v_admin_user" page="4" message=$smarty.capture.invalid_username}
      <br />
      <p>{l}Administrator username{/l}</p>
      <p>{l}The username used to access the administrative pages of phpLinkDirectory. The user name must have minimum 4 characters, maximum 10 characters and must contain only letters and digits.{/l}</p>
   </td>
  </tr>
  <tr>
    <td width="30%">{l}Name{/l}<span class='req'>*</span></td>
    <td width="70%" class="smallDesc">
      <input type="text" name="admin_name" value="{$admin_name}" size="20" maxlength="100" />
      {validate form="install" id="v_admin_name" page="4" message=$smarty.capture.field_required}
      <br />
     <p> {l}Administrator name{/l}</p>
      <p>{l}The name of the administrative user. This name will be used in the <em>From:</em> field when sending emails.{/l}</p>
   </td>
  </tr>
  <tr>
    <td width="30%">{l}Password{/l}<span class='req'>*</span></td>
    <td width="70%" class="smallDesc">
      <input type="password" name="admin_password" value="" size="20" maxlength="25" />
      {validate form="install" id="v_admin_password" page="4" message=$smarty.capture.invalid_password}
      <br />
      <p>{l}Administrator password{/l}</p>
      <p>{l}The password is used together with the username to access the administrative pages of phpLinkDirectory. The password must have minimum 6 characters and maximum 10 characters.{/l}</p>
   </td>
  </tr>
  <tr>
    <td width="30%">{l}Confirm Password{/l}<span class='req'>*</span></td>
    <td width="70%" class="smallDesc">
      <input type="password" name="admin_passwordc" value="" size="20" maxlength="25" />
      {validate form="install" id="v_admin_passwordc" page="4"  message=$smarty.capture.password_not_match}
      <br />
      <p>{l}Confirm administrator password{/l}</p>
      <p>{l}To verify that the password was typed correctly please enter it again.{/l}</p>
   </td>
  </tr>
  <tr>
    <td width="30%">{l}Email{/l}<span class='req'>*</span></td>
    <td width="70%" class="smallDesc">
      <input type="text" name="admin_email" value="{$admin_email}" size="20" maxlength="250" />
      {validate form="install" id="v_admin_email" page="4" message=$smarty.capture.invalid_email}
      <br />
      <p>{l}Administrator Email{/l}</p>
      <p>{l}Administrative email address. This email address will be used for notifications regarding the system.{/l}</p>
   </td>
  </tr>
</table>

{elseif $step ge 5}
<h3>{l}Thank you for choosing phpLinkDirectory.{/l}</h3>
<p>{l}To start setting up the directory access the administrative pages {/l}<a href="{$smarty.const.DOC_ROOT}/admin/login.php">{l}here{/l}</a>.</p>
<p>{l}You can browse the directory {/l}<a href="{$smarty.const.DOC_ROOT}/">{l}here{/l}</a>.</p>
<h3>{l}YOU MUST DELETE THE FOLLOWING FILE(S) BEFORE CONTINUING:{/l} <em>{$smarty.const.DOC_ROOT}/install/index.php</em></h3>
<h3>{l}YOU MUST DROP WRITING PERMISSIONS TO FOLLOWING FILE(S) BEFORE CONTINUING:{/l} <em>{$smarty.const.DOC_ROOT}/include/config.php</em></h3>
{/if}
        
{if $btn_back}
 <input type="submit" name="submit" value="back" title="{l}Go to previous step{/l}" accesskey="b" class="button" />
{/if}
{if not $fatal}
 {if $btn_next}
	<input type="submit" name="submit" value="next" title="{l}Go to next step{/l}" accesskey="n" class="button" />
 {/if}
{/if}
{if $btn_restart}
 <input type="submit" name="submit" value="restart" title="{l}Restart installation/update process.{/l}" accesskey="r" class="button" />
{/if}
      
</form>
</div> <!-- #content -->
</div> <!-- #container -->
</div> <!-- #body-container -->
</body>
</html>