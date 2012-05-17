{if is_array($m.menu)}
	<ul>
	{foreach from=$m.menu item=l key=k}
		<li>
		{if is_array($l.menu)}
				<a class="item" href="javascript:void(0)">
					{$l.label}
					<!-- #TODO<img class="arrow" src="images/arrow1.gif" width="4" height="7" alt="" /> -->
				</a>
			{include file="admin/menu.tpl" m=$l}
		{elseif is_array($l)}
			{if $l.disabled}
			<a class="disabled">{$l.label}</a>
			{else}
			<a class="item" href="{$l.url}{if strpos($l.url, '?')!== false}&amp;r=1{else}?r=1{/if}">{$l.label}</a>
			{/if}
		{else}
		<a class="item" href="{$mk}_{$k}.php?r=1">{$l}</a>
		{/if}
		</li>
	{/foreach}
    </ul>
{/if}
