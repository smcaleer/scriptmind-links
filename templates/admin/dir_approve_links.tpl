{strip}
<form action="" method="Post">
<table border="0" cellpadding="0" cellspacing="0" class="list">
  <tr>
  <th>X</th>
  {foreach from=$columns key=col item=name}
  <th class="listHeader" id="{$col}">
  {if $SORT_FIELD eq $col}
  	{if $SORT_ORDER eq 'ASC'}
  		<img src="images/sort_a.gif" width="16" height="9" class="order"/>
  	{else}
  		<img src="images/sort_d.gif" width="16" height="9" class="order"/>
  	{/if}
  {/if}
  {$name}
  </th>
  {/foreach}
  	<th colspan="3">{l}Action{/l}</th>
  </tr>
 {assign var=doBulk value=true}
 {foreach from=$list item=row key=id}
  <tr class="{cycle values="odd,even"}">
  <td><input type="checkbox" name="BulkDom[]" value="{$id}" /></td>
  {foreach from=$columns key=col item=name}
    {if $col eq 'URL'}
      <td>
      {assign var="s" value=$row.VALID}
      <img src="images/valid_{$s}.gif" width="13" height="13" />
      <a href="{$row.$col}" target="_blank">{$row.$col|regex_replace:"`.+://`":""|truncate:30:"..."}</a>
      <span id="tURL{$id}" class="tt">{$row.$col}</span>
  	{elseif $col eq 'TITLE'}
  		<td><a class="htt" id="T{$id}" href="javascript:void();">{$row.$col}</a>
  		{include file="link_details.tpl" id=$id link=$link}
    {elseif $col eq 'RECPR_URL'}
      <td>
      {assign var="s" value=$row.RECPR_VALID}
      <img src="images/valid_{$s}.gif" width="13" height="13" />
      <a class="htt" id="RURL{$id}" href="{$row.$col}" target="_blank">{$row.$col|regex_replace:"`.+://`":""|truncate:30:"..."}</a>
      <span id="tRURL{$id}" class="tt">{$row.$col}</span>
    {elseif $col eq 'DATE_ADDED'}
      <td>{$row.$col|date_format:$date_format}
    {elseif $col eq 'EXTRA'}
  		<td><a class="htt" id="T{$id}" href="javascript:void();">...</a>
  		{include file="link_extras.tpl" id=$id link=$link}
  	{else}
  		<td>{$row.$col}&nbsp;
  	{/if}
  </td>
  {/foreach}
  	<td align="center"><a href="dir_links_edit.php?action=A:{$id}"><img src="images/a_ok.gif" width="16" height="13" border="0" alt="Approve" /></a></td>
    <td align="center"><a href="dir_links_edit.php?action=E:{$id}"><img src="images/a_edit.gif" width="16" height="13" border="0" alt="Edit" /></a></td>
    <td align="center"><a href="dir_links_edit.php?action=D:{$id}" onclick="return link_rm_confirm('{l}Are you sure you want to remove this link?{/l}\n{l}Note: links can not be restored after removal!{/l}');" title="{l}Remove Link{/l}: {$row.TITLE|escape|trim}"><img src="images/a_delete.gif" width="16" height="13" border="0" alt="Delete" /></a></a></td>
 {foreachelse}
 {assign var=doBulk value=false}
 <tr>
 	<td colspan="10" class="norec">{l}No records found.{/l}</td>
 </tr>
 {/foreach}
 <tr>
 	<td colspan="11" class="norec">{include file="list_pager.tpl"}</td>
 </tr>
</table>
{/strip}
{if $doBulk}
{include file="link_bulk_actions.tpl" categs=$categs category=0 stats=$stats}
{/if}
</form>
<script type="text/javascript" src="files/table.js"></script>
<script type="text/javascript">
/* <![CDATA[ */
   tableInit();
   tooltip_init();
/* ]]> */
</script>