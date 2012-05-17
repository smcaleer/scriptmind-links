   {* Error and confirmation messages *}
   {include file="admin/messages.tpl"}

{strip}
{if $sql_error}
   <div class="warning">
      <img src="images/no_22.gif" alt="" />
      <p>{l}An error occured while saving.{/l}</p>
      <p>{l}The database server returned the following message:{/l}</p>
      <p>{$sql_error|escape|trim}</p>
   </div>
{/if}

{if $posted}
   <div class="alert">
      {l}Email message template saved.{/l}
   </div>
{/if}

<form method="post" action="">
<table border="0" class="formPage">
  <tr>
   <td class="label"><span class='req'>*</span>{l}Title{/l}:</td>
   <td class="smallDesc">
      <input type="text" name="TITLE" value="{$TITLE|escape|trim}" size="40" maxlength="100" class="text" />
      {validate id="v_TITLE" message=$smarty.capture.field_char_required}
   </td>
   <td rowspan="5">
   <div class="info">
      <table border="0" cellspacing="0">
         <tr><td colspan="2" nowrap="nowrap"><img src="images/help_22.gif" alt="Help" align="top" border="0" />{l}Available variables for <i>Subject</i> and <i>Body</i>{/l}</td></tr>
         <tr><td colspan="2">from phpLinkDirectory configuration:</td></tr>
         <tr><td>{literal}{MY_SITE_NAME}{/literal}</td><td> - {l}Site Name{/l}</td></tr>
         <tr><td>{literal}{MY_SITE_URL}{/literal}</td><td> - {l}Site URL{/l}</td></tr>
         <tr><td>{literal}{MY_SITE_DESC}{/literal}</td><td> - {l}Site Description{/l}</td></tr>
         <tr><td colspan="2"><br />from <i>Send Email</i> form:</td></tr>
         <tr><td>{literal}{LINK_TITLE}{/literal}</td><td> - {l}Site Name{/l}</td></tr>
         <tr><td>{literal}{LINK_DESCRIPTION}{/literal}</td><td> - {l}Site Description{/l}</td></tr>
         <tr><td>{literal}{LINK_URL}{/literal}</td><td> - {l}Link URL{/l}</td></tr>
         <tr><td>{literal}{LINK_OWNER_NAME}{/literal}</td><td> - {l}Site Owner Name{/l}</td></tr>
         <tr><td>{literal}{LINK_OWNER_EMAIL}{/literal}</td><td> - {l}Site Owner Email{/l}</td></tr>
         <tr><td>{literal}{LINK_RECPR_URL}{/literal}</td><td> - {l}Reciprocal URL{/l}</td></tr>
         <tr><td colspan="2"><br />from <i>Send Email and Add Link</i> form:</td></tr>
         <tr><td>{literal}{EMAIL_TITLE}{/literal}</td><td> - {l}Site Name{/l}</td></tr>
         <tr><td>{literal}{EMAIL_NAME}{/literal}</td><td> - {l}Site Owner Name{/l}</td></tr>
         <tr><td>{literal}{EMAIL_URL}{/literal}</td><td> - {l}Link URL{/l}</td></tr>
         <tr><td>{literal}{EMAIL_DESCRIPTION}{/literal}</td><td> - {l}Site Description{/l}</td></tr>
         <tr><td>{literal}{EMAIL_ADD_RECIPROCAL_URL}{/literal}</td><td> - {l}URL to enter reciprocal link{/l}</td></tr>
         <tr><td>{literal}{EMAIL_LINK_URL}{/literal}</td><td> - {l}Directory page where link is added{/l}</td></tr>
      </table>
   </div>
   </td>
   </tr>
   <tr>
   <td class="label"><span class='req'>*</span>{l}Template Type{/l}:</td>
   <td class="smallDesc">
      {html_options options=$tpl_types selected=$TPL_TYPE name="TPL_TYPE"}
      {validate id="v_VALIDATE_EMAIL_TYPE" message=$smarty.capture.email_template_already_defined}
   </td>
  </tr>
  <tr>
    <td class="label"><span class='req'>*</span>{l}Subject{/l}:</td>
    <td class="smallDesc">
      <input type="text" name="SUBJECT" value="{$SUBJECT|escape|trim}" size="71" maxlength="255" class="text" />
      {validate id="v_SUBJECT"  message=$smarty.capture.field_char_required}
   </td>
  </tr>
  <tr>
   <td class="label"><span class='req'>*</span>{l}Body{/l}:</td>
   <td class="smallDesc">
      <textarea name="BODY" rows="6" cols="51" class="text">{$BODY|escape|trim}</textarea>
      {validate id="v_BODY" message=$smarty.capture.field_char_required}
   </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="submit" value="Save" class="btn"/></td>
  </tr>
</table>
</form>
{/strip}