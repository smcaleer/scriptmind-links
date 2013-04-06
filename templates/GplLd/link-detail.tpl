{capture name="title"} - {l}Link Details{/l}{/capture}
{capture assign="in_page_title"}{l}Link Details{/l}{/capture}
{capture assign="description"}{l}Details for the link{/l}{/capture}

{include file="header.tpl"}
{include file="navigation.tpl"}

<div id="container">
	<div id="content"> <!-- for categories/links -->
	<div class="content">
		{include file="breadcrumb.tpl"}

		{foreach from=$linkdetail item=thelinkdetail}
			<div class="linkDetail">
				<h2>{$thelinkdetail.TITLE}</h2>
				<div class="screenshot">
					<img src="http://images.websnapr.com/?url={$thelinkdetail.URL}&size=s" alt="{$thelinkdetail.TITLE}" />
				</div>
				<div class="linkInfo">
					<span class="title">Title</span>
					<span class="details"><a href={$thelinkdetail.URL}>{$thelinkdetail.TITLE}</a></span><br/>

					<span class="title">URL</span>
					<span class="details"><a href={$thelinkdetail.URL}>{$thelinkdetail.URL}</a></span><br/>

					<span class="title">Description</span>
					<span class="details">{$thelinkdetail.DESCRIPTION}</span><br/>

					<span class="title">Page Rank</span>
					<span class="details">{$thelinkdetail.PAGERANK}</span<br/>

					<span class="title">Resources</span>
					<span class="details">
						<ul>
                        {foreach from=$deeplinks item=deeplink name=deeplinks}
                            <li><a href="{$deeplink.URL|escape|trim}"><b>{$deeplink.TITLE|escape|trim}</b></a> {$deeplink.DESCRIPTION|escape|trim}</li>
                        {/foreach}
						</ul>
					</span>
				</div>
			</div>

		{/foreach}

	</div> <!-- .content -->

{include file="sidebar.tpl"}

</div> <!-- #content -->
</div> <!-- #container -->
{include file="footer.tpl"}