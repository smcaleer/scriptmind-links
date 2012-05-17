<div id="navigation">
	<div class="">
		<div id="page-nav">
			<ul class="menu">
				<li>
					<a href="{$smarty.const.DOC_ROOT}/">Home</a>
				</li>
				<li>
					<a>Browse</a>
					<ul>
						{foreach from=$topcats item=cat name=categs}
							<li><a href="{$smarty.const.DOC_ROOT}/{if $smarty.const.ENABLE_REWRITE}{$cat.TITLE_URL|escape}/{else}index.php?c={$cat.ID}{/if}">
								{$cat.TITLE|escape}
							</a></li>
						{/foreach}
					</ul>
				</li>
				<li>
					<a href="{$smarty.const.DOC_ROOT}/submit.php{if !empty ($category.ID) and $category.ID > 0}?c={$category.ID}{/if}" title="{l}Submit your link to the directory{/l}">{l}Submit Link{/l}</a>
				</li>
				<li>
					<a href="{$smarty.const.DOC_ROOT}/index.php?p=d" title="{l}Browse latest submitted links{/l}">{l}Latest Links{/l}</a>
				</li>
				<li>
					<a href="{$smarty.const.DOC_ROOT}/index.php?p=h" title="{l}Browse most popular links{/l}">{l}Top Hits{/l}</a>
				</li>
			</ul>
		</div>
		
		{if $smarty.const.ENABLE_RSS and (!empty($qu) or !empty($category.ID) or $p)}
		<div id="rss">
			<a href="{$smarty.const.DOC_ROOT}/rss.php?{if !empty($qu)}q={$qu|@urlencode}{elseif $p}p={$p}{else}c={$category.ID}{/if}">
				<img src="{$smarty.const.DOC_ROOT}/templates/images/feed.png" align="top" alt="RSS Feed" border="0" />
			</a>
		</div>
		{/if}

		<div id="search">
			<form action="{$smarty.const.DOC_ROOT}/index.php" method="get">
				<input type="text" name="q" size="15" class="text" value="{if !empty($qu)}{$qu|escape}{/if}" />
				<input type="submit" value="{l}Search{/l}" class="submit" />
			</form>
		</div>
	</div>
</div> <!-- #navigation -->
