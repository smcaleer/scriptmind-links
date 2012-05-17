{strip}
<form method="post" action="">
<table border="0" class="formPage" width="100%">
  <tr>
  	<th>{l}Start Date{/l}</th>
  	<td class="smallDesc">
  		{html_select_date prefix="SD" time=$SD start_year="-5" end_year="+1"}
  	</td>
  </tr>
  <tr>
  	<th>{l}End Date{/l}</th>
  	<td class="smallDesc">
  		{html_select_date prefix="ED" time=$ED start_year="-5" end_year="+1"}
  	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="filter" value="Filter" class="btn"/>
    	{*&nbsp;<input type="submit" name="email" value="Email Report" class="btn"/></td>*}
  </tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" class="list">
  <tr>
  {foreach from=$columns key=col item=name name=cols}
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
  </tr>
 {foreach from=$list item=row key=id}
  <tr class="{cycle values="odd,even"}">
  {foreach from=$columns key=col item=name}
  	<td>
      {if $col eq 'DATE_SENT'}
      {$row.$col|date_format:$date_format}
      {else}
  		{$row.$col}&nbsp;
  	  {/if}
  	</td>
  {/foreach}
 {foreachelse}
 <tr>
 	<td colspan="5" class="norec">{l}No records found.{/l}</td>
 </tr>
 {/foreach}
 <tr>
 	<td colspan="5" class="norec">{include file="admin/list_pager.tpl"}</td>
 </tr>
</table>
<script type="text/javascript" src="files/table.js"></script>
<script>
tableInit();
</script>
{/strip}