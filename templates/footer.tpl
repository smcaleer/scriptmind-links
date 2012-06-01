{* Pager (not used yet) *}
{if $list_total gt 0}
   <div id="pagenavi">
         {if $smarty.const.ENABLE_REWRITE}
            {assign var='url_pattern' value='?p=$'}
         {/if}
		{pager rowcount=$list_total limit=$smarty.const.PAGER_LPP class_num="page" class_numon="current" class_text="" posvar="p" show="page" txt_next="Next" txt_prev="Previous" shift="1" separator="" wrap_numon="" url_pattern="$url_pattern"}

      <span class="pages">{l}Total records:{/l} {$list_total}</span>
   </div>
{/if}

<div id="footer-container">
	<div id="footer">
		<p class="copyright">
			&copy; {$smarty.now|date_format:"%Y"} {$smarty.const.DIRECTORY_TITLE}
		</p>
		<p class="credit">
            <a href="{$smarty.const.DOC_ROOT}/sitemap.php" title="{l}Full site navigation{/l}">{l}Sitemap{/l}</a>
			Powered by <a href="http://www.scriptmind.org" title="ScriptMind::Links Script">ScriptMind::Links</a>
		</p>
	</div> <!-- #footer -->
</div> <!-- #footer-container -->

</div> <!-- #body-container -->
</body>
</html>