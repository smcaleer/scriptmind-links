   {* Error and confirmation messages *}
   {include file="admin/messages.tpl"}

{strip}
{php}
   $this->assign('opt_bool', array(1 => $this->translate('Yes'), 0 => $this->translate('No')));
{/php}

{if $posted}
   <div class="alert">
      {l}Settings updated.{/l}
   </div>
{/if}

<form method="post" action="">
<table border="0" class="formPage">
{assign var="categ" value="0"}
{foreach from=$conf item=row}
   {if $categ ne $row.CONFIG_GROUP}
      {assign var="categ" value=$row.CONFIG_GROUP}
      <tr><th colspan="2">{$conf_categs.$categ|trim}</th></tr>
   {/if}
   <tr>
      <th>{if $row.REQUIRED eq 1}<span class='req'>*</span>{/if}{$row.NAME|escape}:</th>
      <td  class="smallDesc">
         {if $row.TYPE eq 'STR'}
            <input type="text" name="{$row.ID}" value="{$row.VALUE}" size="40" maxlength="255" class="text"/>
            {validate form="conf_settings" id="v_`$row.ID`" message=$smarty.capture.field_char_required}
         {elseif $row.TYPE eq 'PAS'}
            <input type="password" name="{$row.ID}" value="{$row.VALUE}" size="40" maxlength="255" class="text"/>
            {validate form="conf_settings" id="v_`$row.ID`" message=$smarty.capture.field_pass_required}
         {elseif $row.TYPE eq 'URL'}
            <input type="text" name="{$row.ID}" value="{$row.VALUE}" size="40" maxlength="255" class="text"/>
            {validate form="conf_settings" id="v_`$row.ID`" message=$smarty.capture.invalid_url}
         {elseif $row.TYPE eq 'INT'}
            <input type="text" name="{$row.ID}" value="{$row.VALUE}" size="10" maxlength="20" class="text"/>
            {validate form="conf_settings" id="v_`$row.ID`" message=$smarty.capture.field_integer_required}
         {elseif $row.TYPE eq 'NUM'}
            <input type="text" name="{$row.ID}" value="{$row.VALUE}" size="10" maxlength="20" class="text"/>
            {validate form="conf_settings" id="v_`$row.ID`" message=$smarty.capture.field_num_required}
         {elseif $row.TYPE eq 'LOG'}
            {html_options options=$opt_bool selected=$row.VALUE name=$row.ID}
            {validate form="conf_settings" id="v_`$row.ID`" message=$smarty.capture.field_char_required}
         {elseif $row.TYPE eq 'LKP'}
            {html_options options=$row.OPTIONS selected=$row.VALUE name=$row.ID}
            {validate form="conf_settings" id="v_`$row.ID`" message=$smarty.capture.field_char_required}
         {/if}
         <div style="clear: both">{$row.DESCRIPTION}</div>
      </td>
   </tr>
{/foreach}
   <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="submit" value="Save" class="btn" /></td>
   </tr>
</table>
</form>
{/strip}