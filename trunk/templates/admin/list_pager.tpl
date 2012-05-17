{strip}
{if $list_total gt 0}
<div class="navig">
<div style="float: right">{pager rowcount=$list_total limit=$list_limit class_num="" class_numon="list-nav-active"
	class_text="" posvar="p" show="page"
	img_next="images/next.gif" img_prev="images/prev.gif" shift="1" separator=" " wrap_numon="[|]"}
</div>{l}Total records:{/l} {$list_total}
</div>
{/if}
{/strip}