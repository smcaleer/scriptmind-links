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
						<li><a href="{$thelinkdetail.URL1|escape|trim}">{$thelinkdetail.TITLE1|escape|trim}</a></li>
						<li><a href="{$thelinkdetail.URL2|escape|trim}">{$thelinkdetail.TITLE2|escape|trim}</a></li>
						<li><a href="{$thelinkdetail.URL3|escape|trim}">{$thelinkdetail.TITLE3|escape|trim}</a></li>
						<li><a href="{$thelinkdetail.URL4|escape|trim}">{$thelinkdetail.TITLE4|escape|trim}</a></li>
						<li><a href="{$thelinkdetail.URL5|escape|trim}">{$thelinkdetail.TITLE5|escape|trim}</a></li>
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