   {* Error and confirmation messages *}
   {include file="admin/messages.tpl"}

{l}Importing into:{/l}
{assign var="current_path" value=""}
<em>
   {foreach from=$path item=cat name=path}
      {assign var="current_path" value="`$current_path``$cat.TITLE_URL`/"}
      {if not $smarty.foreach.path.first} &raquo; {/if}
      {if not $smarty.foreach.path.last}
         {$cat.TITLE|escape}
      {else}
         {$cat.TITLE|escape}
      {/if}
   {/foreach}
</em>

{if $error}
   <div class="warning">
      <p>{l}An error occured while parsing the RSS feed. Please make sure that the specified URL is a valid RSS feed.{/l}</p>
      <p>{l}The RSS parser returned the following message:{/l}</p>
      <p>{$error|escape}</p>
   </div>
{elseif $link_count eq 0}
   <div class="warning">
      <p>{l}No links were found in the RSS feed.{/l}</p><br /><br />
   </div>
{/if}
<form name="form1" method="post" action="dir_links_importrss.php?c={$cid}">
<table border="0" class="formPage">
    <tr>
      <th><span class='req'>*</span>{l}RSS feed URL{/l}:</th>
      <td class="smallDesc"><input name="rss_url" type="text" class="text" value="{$rss_url}" id="rss_url" size="40">
      {validate id="v_URL" message=$smarty.capture.invalid_url}</td>
    </tr>
    <tr>
      <th><span class='req'>*</span>{l}Import as{/l}:</th>
      <td class="smallDesc">
        <input name="status" type="radio" value="2" checked> {l}Active{/l} &nbsp;
      <input name="status" type="radio" value="0">{l}Inactive{/l}</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input name="submit" type="submit" class="btn" value="{l}Import{/l}"></td>
    </tr>
  </table>
</form>

<p>&nbsp;</p>

{if $list}
<table border="0" cellpadding="0" cellspacing="0" class="list">
   <tr>
      <th>{l}Title{/l}</th>
      <th>{l}URL{/l}</th>
      <th>{l}Result{/l}</th>
   </tr>
   {foreach from=$list item=row key=id}
   <tr class="{cycle values="odd,even"}">
      <td>{$row.TITLE|escape}</td>
      <td>{$row.URL|escape}</td>
      <td>
         {if $row.ERROR.TITLE}{l}<img src="images/stat_0.gif" width="9" height="9" border="0" alt=""/>Title already exists{/l}
         {elseif $row.ERROR.URL}{l}<img src="images/stat_0.gif" width="9" height="9" border="0" alt=""/>URL already exists{/l}
         {elseif $row.ERROR.SQL}{l}<img src="images/stat_0.gif" width="9" height="9" border="0" alt=""/> SQL error while inserting{/l}
         {else}<img src="images/stat_2.gif" width="9" height="9" border="0"/> Link imported{/if}
      </td>
   </tr>
   {/foreach}
</table>
{/if}