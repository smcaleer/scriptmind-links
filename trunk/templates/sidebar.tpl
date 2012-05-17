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
				<li><a title="ScriptMind::Links" href="http://www.scriptmind.org/">gplLD</a></li>
				<li><a title="ScriptMind::Links Support Forums" href="http://www.scriptmind.org/">ScriptMind::Links Support Forums</a></li>
				<li><a title="ScriptMind::Links Themes" href="http://www.scriptmind.org/">ScriptMind::Links Themes</a></li>
				<li><a title="ScriptMind::Links Plugins" href="http://www.scriptmind.org/">ScriptMind::Links Plugins</a></li>
			</ul><!-- .xoxo -->
		</div>

</div>
