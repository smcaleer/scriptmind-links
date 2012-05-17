{capture name="title"} - {l}Rate A Link{/l}{/capture}
{capture assign="in_page_title"}{l}Add Reciprocal Link{/l}{/capture}
{capture assign="description"}{l}Add a valid reciprocal link back to this website{/l}{/capture}

{include file="header.tpl"}
{include file="top_bar.tpl"}

{strip}
{if $link_id_error}
   <div class="err">
      <img src="admin/images/no_22.gif" alt="Error" />
      <p>{l}Invalid Link referenced.{/l}</p>
      <p>{$link_id_error}</p>
   </div>
{/if}
{if $sql_error}
   <div class="err">
      <img src="admin/images/no_22.gif" alt="Error" />
      <p>{l}An error occured while saving.{/l}</p>
      <p>{l}The database server returned the following message:{/l}</p>
      <p>{$sql_error}</p>
   </div>
{/if}
<form method="post" action="">
<table border="0" class="formPage">
{if $error}
   <tr>
      <td colspan="2" class="err">
         {l}An error occured while adding your reciprocal link.{/l}
      </td>
   </tr>
{/if}

{if $posted}
   <tr>
      <td colspan="2" class="msg">
         {l}Your reciprocal link has been successfully added.{/l}
      </td>
   </tr>
{/if}

{if empty($posted) and !$link_id_error}
   <tr>
   <td colspan="2">
         <a id="id_{$ID}" href="{$URL|escape|trim}" title="{$TITLE|escape|trim}"
         {* nofollow *}
         {if $link.NOFOLLOW or ($link.RECPR_VALID eq 0 and ($smarty.const.RECPR_NOFOLLOW eq 2 or ($smarty.const.RECPR_NOFOLLOW eq 1 and $link.RECPR_REQUIRED eq 1)))} rel="nofollow"{/if}
         {if $smarty.const.ENABLE_BLANK} target="_blank"{/if}>
         {$TITLE|escape|trim}</a> <span class="url">- {$URL|escape|trim}</span>
         <p>{$DESCRIPTION|escape|trim}</p>
   </td>
   </tr>
   <tr>
      <td class="label"><span class='req'>*</span>{l}Reciprocal Link URL{/l}:</td>
      <td class="field">
         <input type="text" name="RECPR_URL" value="{$RECPR_URL|trim}" size="40" maxlength="255" class="text" />
         {validate form="add_reciprocal" id="v_RECPR_URL" message=$smarty.capture.invalid_url}
         {validate form="add_reciprocal" id="v_RECPR_ONLINE" message=$smarty.capture.recpr_not_found|replace:'#SITE_URL#':$smarty.const.SITE_URL}
      </td>
   </tr>
   <tr>
      <td colspan="2" class="buttons"><input type="submit" name="submit" value="Add" class="btn" /></td>
   </tr>
{/if}
</table>
</form>
{include file="footer.tpl"}
{/strip}