{strip}
<a href="dir_links_edit.php?action=N{if $featured}&f=1{/if}" class="button">{l}New Link{/l}</a>
{if $rss_link eq true}
&nbsp;&nbsp;<a href="dir_links_importrss.php?c={$category}" class="button">{l}Import RSS{/l}</a>
{/if}
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
  	<th colspan="2">{l}Action{/l}</td>
  </tr>
 {foreach from=$list item=row key=id}
  <tr class="{if $category and $row.FEATURED}featured{else}{cycle values="odd,even"}{/if}">
  {foreach from=$columns key=col item=name}
  {assign var="val" value=$row.$col}
  	{if $col eq 'STATUS'}
  		<td>
  		<a href="javascript:void(0);" class="pop" id="S{$id}" ><img src="images/stat_{$val}.gif" width="9" height="9" border="0"/> {$stats[$val]}</a>
  		<div class="pop-list" id="pS{$id}">
  			<span>Set new status:</span>
  			{foreach from=$stats item=v key=k}
  			{if $k ne $val and $k ne 1}
  				<a href="dir_links_edit.php?action=S:{$id}:{$k}"><img src="images/stat_{$k}.gif" width="9" height="9" border="0"/> {$stats[$k]}</a><br />
  			{/if}
  			{/foreach}
  		</div>
  	{elseif $col eq 'LINK_TYPE'}
  		<td>{$link_type_str.$val}
  	{elseif $col eq 'TITLE'}
  		<td><a class="htt" id="T{$id}" href="javascript:void(0);">{$row.$col}</a>
  		{include file="admin/link_details.tpl" id=$id link=$link}
    {elseif $col eq 'URL'}
      <td>
      {assign var="s" value=$row.VALID}
      <img src="images/valid_{$s}.gif" width="13" height="13" />
      <a class="htt" id="URL{$id}" href="{$row.$col}" target="_blank">{$row.$col|regex_replace:"`.+://`":""|truncate:30:"..."}</a>
      <span id="tURL{$id}" class="tt">{$row.$col}</span>
    {elseif $col eq 'PAGERANK'}
      <td>
      {if $row.$col eq -1}N/A{else}{$row.$col}{/if}
    {elseif $col eq 'DATE_ADDED'}
      <td>{$row.$col|date_format:$date_format}
  	{else}
  		<td>{$row.$col}&nbsp;
  	{/if}
  </td>
  {/foreach}
    <td align="center"><a href="dir_links_edit.php?action=E:{$id}"><img src="images/a_edit.gif" width="16" height="13" border="0" alt="Edit" /></a></td>
    <td align="center"><a href="dir_links_edit.php?action=D:{$id}" onclick="return link_rm_confirm('{l}Are you sure you want to remove this link?{/l}\n{l}Note: links can not be restored after removal!{/l}');" title="{l}Remove Link{/l}: {$row.TITLE|escape|trim}"><img src="images/a_delete.gif" width="16" height="13" border="0" alt="Delete" /></a></td>
 {foreachelse}
 <tr>
 	<td colspan="{$col_count}" class="norec">{l}No records found.{/l}</td>
 </tr>
 {/foreach}
 <tr>
 	<td colspan="{$col_count}" class="norec">{include file="admin/list_pager.tpl"}</td>
 </tr>
</table>
<script type="text/javascript" src="files/table.js"></script>
<script type="text/javascript">
/* <![CDATA[ */
   tableInit();
   tooltip_init();
   pop_list_init();
/* ]]> */
</script>
{/strip}