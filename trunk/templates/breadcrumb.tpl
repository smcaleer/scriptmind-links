<div class="breadcrumb breadcrumbs">
	<div class="breadcrumb-trail">
		{* Display current path *}
		<span class="breadcrumb-title">Browse:</span>
		{assign var="current_path" value=""}
		{foreach from=$path item=cat name=path}
			{assign var="current_path" value="`$current_path``$cat.TITLE_URL`/"}
			{if !$smarty.foreach.path.first} &raquo; {/if}
			{if !$smarty.foreach.path.last}
				<a href="{if $smarty.const.ENABLE_REWRITE}{$current_path}{else}index.php?c={$cat.ID}{/if}">{$cat.TITLE|escape|trim}</a>
				{else}
				<a href="{$smarty.const.DOC_ROOT}">
					{$cat.TITLE|escape|trim}
				</a>
			{/if}
		{/foreach}
	</div>
</div>