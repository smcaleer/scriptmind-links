<div class="aside" id="primary">
	<div class="widget">
		<div class="widget-inside">
			<h3 class="widget-title">Categories</h3>
			<ul class="xoxo">
				{foreach from=$topcats item=cat name=categs}
					<li><a href="{$smarty.const.DOC_ROOT}/{if $smarty.const.ENABLE_REWRITE}{$cat.TITLE_URL|escape}/{else}index.php?c={$cat.ID}{/if}">
						{$cat.TITLE|escape}
					</a></li>
				{/foreach}
			</ul><!-- .xoxo -->
		</div>
	</div>
</div>
<div class="aside" id="secondary">
	<div class="widget pages widget-pages" id="hybrid-pages-4">
		<div class="widget-inside">
			<h3 class="widget-title">Links</h3>
			<ul class="xoxo">
				<li><a title="GPL Link Directory" href="http://www.gplld.com">gplLD</a></li>
				<li><a title="gplLD Support Forums" href="http://www.gplld.com/forums/">gplLD Support Forums</a></li>
				<li><a title="gplLD Themes" href="http://www.gplld.com/extend/themes/">gplLD Themes</a></li>
				<li><a title="gplLD Plugins" href="http://www.gplld.com/extend/plugins/">gplLD Plugins</a></li>
			</ul><!-- .xoxo -->
		</div>
		
</div>
