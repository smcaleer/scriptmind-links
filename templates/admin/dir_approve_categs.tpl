{strip}
<table border="0" cellpadding="0" cellspacing="0" class="list">
  <tr>
  {foreach from=$columns key=col item=name}
  <th id="{$col}">
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
 {foreach from=$list item=row key=id}
  <tr class="{cycle values="odd,even"}">
  {foreach from=$columns key=col item=name}
    {if $col eq 'URL'}
      <td>
      {assign var="s" value=$row.VALID}
      <img src="images/valid_{$s}.gif" width="13" height="13" />{$row.$col}
    {elseif $col eq 'RECPR_URL'}
      <td>
      {assign var="s" value=$row.RECPR_VALID}
      <img src="images/valid_{$s}.gif" width="13" height="13" />{$row.$col}   
    {elseif $col eq 'DATE_ADDED'}
      <td>{$row.$col|date_format:$date_format}
  	{else}
  		<td>{$row.$col}&nbsp;
  	{/if}
  </td>
  {/foreach}
  	<td align="center"><a href="dir_categs_edit.php?action=A:{$id}"><img src="images/a_ok.gif" width="16" height="13" border="0" alt="Approve" /></a></td>
    <td align="center"><a href="dir_categs_edit.php?action=E:{$id}"><img src="images/a_edit.gif" width="16" height="13" border="0" alt="Edit" /></a></td>
    <td align="center"><a href="javascript:if(confirm ('Are you sure you want to delete the category?'))window.location.href='dir_categs_edit.php?action=D:{$id}';"><img src="images/a_delete.gif" width="16" height="13" border="0" alt="Delete" /></a></td>
 {foreachelse}
 <tr>
 	<td colspan="7" class="norec">{l}No records found.{/l}</td>
 </tr>
 {/foreach}
 <tr>
 	<td colspan="7" class="norec">{include file="admin/list_pager.tpl"}</td>
 </tr>
</table>

<script type="text/javascript" src="files/table.js"></script>
<script>
tableInit();
</script>
{/strip}