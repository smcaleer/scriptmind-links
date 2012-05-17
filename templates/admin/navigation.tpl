<div id="page-nav">
	<ul class="menu">
	{foreach from=$menu item=mm key=mk}
		<li>
		<a class="button" href="{if is_array($mm)}javascript:void(0){else}{$mk}.php{/if}">
			<!-- #TODO <img src="images/m_{$mk}.gif" border="0" alt="" /> -->
			{if is_array($mm)}
				{$mm.label}
			{else}
				{$mm}
			{/if}
		</a>
		{include file="admin/menu.tpl" m=$mm}
		</li>
	{/foreach}
	</ul>
</div>