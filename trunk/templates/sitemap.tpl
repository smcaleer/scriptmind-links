{capture name="title"} - {l}Sitemap{/l}{/capture}
{capture assign="in_page_title"}{l}Sitemap{/l}{/capture}
{capture assign="description"}{l}Sitemap{/l}{/capture}

{include file="header.tpl"}
{include file="navigation.tpl"}

<div id="container">
	<div id="content"> <!-- for categories/links -->
	<div class="content">
		{*include file="breadcrumb.tpl"*}
	{foreach from=$Categories item=cat name=categs}
        {* for $i = 1 to $cat->depth }&nbsp;&nbsp;&nbsp;{/for *}
        {$indent = str_repeat( '&nbsp;&nbsp;&nbsp;', $cat->depth )}
        {$indent}
        {if $cat->linkable}
            {*<a href="{if $smarty.const.ENABLE_REWRITE}{$cat->built_url|escape}{else}index.php?c={$cat->id}{/if}">*}
            <a href="{$cat->built_url|escape}">
            <b>{$cat->title|escape}</b></a>
        {else}
            <b>{$cat->title|escape}</b>
        {/if}{$cat->description|escape}<br/>
    {/foreach}

</div> <!-- .content -->

{include file="sidebar.tpl"}

</div> <!-- #content -->
</div> <!-- #container -->
{include file="footer.tpl"}

