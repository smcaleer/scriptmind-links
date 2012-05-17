   {* Error and confirmation messages *}
   {include file="admin/messages.tpl"}

{strip}
{if $sql_error}
   <div class="warning">
   <img src="images/no_22.gif" alt="" />
   <p>{l}An error occured while saving.{/l}</p>
   <p>{l}The database server returned the following message:{/l}</p>
   <p>{$sql_error}</p>
</div>
{/if}

{if $posted}
   <div class="alert">
      {l}User saved.{/l}
   </div>
{/if}

<form method="post" action="">
<table border="0" class="formPage">
{if $posted}
  <tr><th colspan="2">{l}Create new user{/l}</th></tr>
{/if}
  <tr>
   <th><span class='req'>*</span>{l}Login{/l}:</th>
   <td class="smallDesc">
      <input type="text" name="LOGIN" value="{$LOGIN|trim}" size="30" maxlength="100" class="text" />
      {validate form="conf_users_edit" id="v_LOGIN" message=$smarty.capture.field_char_required}
      {validate form="conf_users_edit" id="v_LOGIN_U" message=$smarty.capture.login_not_unique}
   </td>
  </tr>
  <tr>
   <th><span class='req'>*</span>{l}Name{/l}:</th>
   <td class="smallDesc">
      <input type="text" name="NAME" value="{$NAME|trim}" size="30" maxlength="100" class="text" />
      {validate form="conf_users_edit" id="v_NAME" message=$smarty.capture.field_char_required}
   </td>
  </tr>
  <tr>
    <th>{l}Password{/l}:</th>
    <td class="smallDesc">
      <input type="password" name="PASSWORD" value="" size="30" maxlength="100" class="text" />
      {validate form="conf_users_edit" id="v_PASSWORD" message=$smarty.capture.field_pass_required}
   </td>
  </tr>
  <tr>
    <th>{l}Confirm Password{/l}:</th>
    <td class="smallDesc">
      <input type="password" name="PASSWORDC" value="" size="30" maxlength="100" class="text" />
      {validate form="conf_users_edit" id="v_PASSWORDC" message=$smarty.capture.password_not_match}
   </td>
  </tr>
  <tr>
    <th><span class='req'>*</span>{l}Email{/l}:</th>
    <td class="smallDesc">
      <input type="text" name="EMAIL" value="{$EMAIL|trim}" size="30" maxlength="255" class="text" />
      {validate form="conf_users_edit" id="v_EMAIL" message=$smarty.capture.invalid_email}
      {validate form="conf_users_edit" id="v_EMAIL_U" message=$smarty.capture.email_not_unique}
   </td>
  </tr>
  <tr>
    <th><span class='req'>*</span>{l}User Type{/l}:</th>
    <td class="smallDesc">
      {html_options options=$admin_user selected=$ADMIN name="ADMIN"}
   </td>
  </tr>
  <tr>
    <th>{l}Link Submit Notification{/l}:</th>
    <td class="smallDesc">
      <input type="checkbox" name="SUBMIT_NOTIF" value="1"{if $SUBMIT_NOTIF} checked="checked"{/if} />
   </td>
  </tr>
  <tr>
    <th>{l}Link Payment Notification{/l}:</th>
    <td class="smallDesc">
      <input type="checkbox" name="PAYMENT_NOTIF" value="1"{if $PAYMENT_NOTIF} checked="checked"{/if} />
   </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="submit" value="Save" class="btn" /></td>
  </tr>
</table>
</form>
{/strip}