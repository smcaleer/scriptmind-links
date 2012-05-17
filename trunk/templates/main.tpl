{* Calculate title from path *}
{capture name="title"}
   {if count($path) > 1} - {/if}
   {foreach from=$path item=category name=path}
      {if $smarty.foreach.path.iteration gt 2}
         &gt;
      {/if}
      {if not $smarty.foreach.path.first}
         {$category.TITLE}
      {/if}
   {/foreach}
{/capture}

{if $title_prefix}
   {assign var="in_page_title" value=$title_prefix|cat:$category.TITLE}
{else}
   {assign var="in_page_title" value=$category.TITLE}
{/if}
{assign var="description" value=$category.DESCRIPTION}

{include file="header.tpl"}

{include file="navigation.tpl"}


<div id="container">
	<div id="content"> <!-- for categories/links -->
	<div class="content">
		{include file="breadcrumb.tpl"}
		
{* ***** Categories ***** *}		
		{* Calculate the number of categories per row *}
		{php}
		   $this->assign('cats_per_col', ceil(count($this->get_template_vars('categs')) / CATS_PER_ROW));
		{/php}

		{if $cats_per_col > 15}
		   {php}
			  $this->assign('cats_per_col', ceil(count($this->get_template_vars('categs')) / (CATS_PER_ROW + 1)));
		   {/php}
		{/if}

		{* Categories *}
		{if !empty($categs)}
			<div id="categories">
			{if !empty($category.ID)}
				<h3>{l}Categories{/l}</h3>
			{/if}

			{foreach from=$categs item=cat name=categs}
				<div class="categories">
				{if $category.ID gt 0}<h4>{else}<h3>{/if}
					<a href="{if $smarty.const.ENABLE_REWRITE}{$cat.TITLE_URL|escape}/{else}index.php?c={$cat.ID}{/if}">
						{$cat.TITLE|escape}
					</a>
					{if $smarty.const.CATS_COUNT}
						<span class="count">({$cat.COUNT})</span>
					{/if}
				{if $category.ID gt 0}</h4>{else}</h3>{/if}
				
				{* Display subcategories *}
				{if !empty($cat.SUBCATS)}
				<ul class="sub-categories">
					{foreach from=$cat.SUBCATS item=scat name=scategs}
						<li>
							<a href="{if $smarty.const.ENABLE_REWRITE}{$cat.TITLE_URL|escape}/{$scat.TITLE_URL|escape}/{else}index.php?c={$scat.ID}{/if}">
								{$scat.TITLE|escape}
							</a>
						</li>
					{/foreach}
				</ul>
					{/if}
				</div> <!-- .categories -->
			{/foreach}
			</div>
		{/if}
<br class="clear"/>

{* ***** Featured Links ***** *}
		{if $smarty.const.FTR_ENABLE == 1 and isset($feat_links) and !empty($feat_links)}
		   <div id="featured-links">
			  <h3>{l}Featured Links{/l}</h3>
			  {foreach from=$feat_links item=link name=links}
				 {include file="link.tpl" link=$link}
			  {/foreach}
		   </div>
		{/if}
{* ***** Normal Links ***** *}
		{* Links heading and sorting*}
		{if ($qu or $category.ID gt 0 or $p) and isset($links) and !empty($links)}
		   <div id="links">
			{l}Links{/l}
			{if not $p}
				<span class="small" style="margin-left:50px;">
					{l}Sort by{/l} : 
					
					{if $smarty.const.ENABLE_PAGERANK and $smarty.const.SHOW_PAGERANK}
						{if $sort eq 'P'}
							<span class="sort">{l}PageRank{/l}</span>
						{else}
							<a href="?s=P{if not $smarty.const.ENABLE_REWRITE}&amp;c={$category.ID}{/if}{if $qu}&amp;q={$qu}{/if}{if !empty($p)}&amp;p={$p}{/if}"> {l}PageRank{/l}</a>
						{/if}
					{/if}
					
					{if $sort eq 'H'}
						<span class="sort">{l}Hits{/l}</span>
					{else}
						<a href="?s=H{if not $smarty.const.ENABLE_REWRITE}&amp;c={$category.ID}{/if}{if $qu}&amp;q={$qu}{/if}{if !empty($p)}&amp;p={$p}{/if}">{l}Hits{/l}</a>
					{/if}
					
					{if $sort eq 'A'}
						<span class="sort">{l}Alphabetical{/l}</span>
					{else}
						<a href="?s=A{if not $smarty.const.ENABLE_REWRITE}&amp;c={$category.ID}{/if}{if $qu}&amp;q={$qu}{/if}{if !empty($p)}&amp;p={$p}{/if}">{l}Alphabetical{/l}</a>
					{/if}
				</span>
			{/if}
			
			{foreach from=$links item=link name=links}
				{include file="link.tpl" link=$link}
			{/foreach}
		   </div>
		{/if}
	</div> <!-- .content -->
	
	{include file="sidebar.tpl"}
	
	</div> <!-- #content -->
</div> <!-- #container -->
{include file="footer.tpl"}