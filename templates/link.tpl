<div class="link">
<div class="col1">
{* show page rank *}
{if $smarty.const.SHOW_PAGERANK}
{include file="pagerank.tpl" pr=$link.PAGERANK}
{/if}
</div>
<div class="col2">
<a
	id="id_{$link.ID}"
	href="{$link.URL|escape|trim}"
	title="{$link.TITLE|escape|trim}"
	{if $link.NOFOLLOW or ($link.RECPR_VALID eq 0 and ($smarty.const.RECPR_NOFOLLOW eq 2 or ($smarty.const.RECPR_NOFOLLOW eq 1 and $link.RECPR_REQUIRED eq 1)))}
		rel="nofollow"
	{/if}
	{if $smarty.const.ENABLE_BLANK}
		target="_blank"
	{/if}
>
	{$link.TITLE|escape|trim}
</a>
<span class="url">
	{$link.URL|escape|trim}
</span>
<p>
	{$link.DESCRIPTION|escape|trim}
	<a href="{$smarty.const.DOC_ROOT}/{$link.CATEGORY_URL|escape|trim}{$link.TITLE|escape|trim|replace:' ':'-'}-{$link.ID}.html" class="read-more">Read More</a>
</p>
</div>
</div>