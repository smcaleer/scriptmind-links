{strip}
<a href="conf_users_edit.php?action=N" class="button">New User</a>
<table border="0" cellpadding="0" cellspacing="0" class="list">
  <tr>
  {foreach from=$columns key=col item=name}
  {if $ENABLE_REWRITE or $col ne 'TITLE_URL'}
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
  {/if}
  {/foreach}
  	<th colspan="3">{l}Action{/l}</th>
  </tr>
 {foreach from=$list item=row key=id}
  <tr class="{cycle values="odd,even"}">
  {foreach from=$columns key=col item=name}
  {if $ENABLE_REWRITE or $col ne 'TITLE_URL'}
  <td>
  	{if $col eq 'ADMIN'}
  		{assign var="s1" value=$row.$col}{$admin_user[$s1]}
  	{elseif $col eq 'SUBMIT_NOTIF'}
  		{assign var="s2" value=$row.$col}{$yes_no[$s2]}
	{elseif $col eq 'PAYMENT_NOTIF'}
  		{assign var="s3" value=$row.$col}{$yes_no[$s3]}
  	{else}
  		{$row.$col}&nbsp;
  	{/if}
  </td>
  {/if}
  {/foreach}
    <td align="center">{if $row.ADMIN eq 0}{if $id ne $current_user_id}<a href="conf_user_permissions.php?action=N:0&u={$id}"><img src="images/a_new.gif" width="16" height="13" border="0" alt="Permissions" /></a>{/if}{/if}</td>
    <td align="center">{if $current_user_is_admin eq 1 or $id eq $current_user_id}<a href="conf_users_edit.php?action=E:{$id}"><img src="images/a_edit.gif" width="16" height="13" border="0" alt="Edit" /></a>{/if}</td>
    <td align="center">{if $current_user_is_admin eq 1}<a href="javascript:if(confirm ('Are you sure you want to delete this user?'))window.location.href='conf_users_edit.php?action=D:{$id}';"><img src="images/a_delete.gif" width="16" height="13" border="0" alt="Delete" /></a>{/if}</td>
 {foreachelse}
 <tr>
 	<td colspan="{if $ENABLE_REWRITE}9{else}8{/if}" class="norec">{l}No records found.{/l}</td>
 </tr>
 {/foreach}
 <tr>
 	<td colspan="{if $ENABLE_REWRITE}10{else}9{/if}" class="norec">{include file="admin/list_pager.tpl"}</td>
 </tr>
</table>
<script type="text/javascript" src="files/table.js"></script>
<script>
tableInit();
</script>
{/strip}