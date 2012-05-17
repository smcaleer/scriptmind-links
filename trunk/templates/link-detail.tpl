{capture name="title"} - {l}Link Details{/l}{/capture}
{capture assign="in_page_title"}{l}Link Details{/l}{/capture}
{capture assign="description"}{l}Details for the link{/l}{/capture}

{include file="header.tpl"}
{include file="navigation.tpl"}

<div id="container">
	<div id="content"> <!-- for categories/links -->
	<div class="content">
		{include file="breadcrumb.tpl"}
		
		{foreach from=$linkdetail item=linkdetail}
			<div class="linkDetail">
				<h2>{$linkdetail.TITLE}</h2>
				<div class="screenshot">
					<img src="http://images.websnapr.com/?url={$linkdetail.URL}&size=s" alt="{$linkdetail.TITLE}" />
				</div>
				<div class="linkInfo">
					<span class="title">Title</span>
					<span class="details"><a href={$linkdetail.URL}>{$linkdetail.TITLE}</a></span><br/>
						
					<span class="title">URL</span>
					<span class="details"><a href={$linkdetail.URL}>{$linkdetail.URL}</a></span><br/>
					
					<span class="title">Description</span>
					<span class="details">{$linkdetail.DESCRIPTION}</span><br/>
					
					<span class="title">Page Rank</span>
					<span class="details">{$linkdetail.PAGERANK}</span<br/>
					
					<span class="title">Resources</span>
					<span class="details">
						<ul>
						<li><a href="{$linkdetail.URL1|escape|trim}">{$linkdetail.TITLE1|escape|trim}</a></li>
						<li><a href="{$linkdetail.URL2|escape|trim}">{$linkdetail.TITLE2|escape|trim}</a></li>
						<li><a href="{$linkdetail.URL3|escape|trim}">{$linkdetail.TITLE3|escape|trim}</a></li>
						<li><a href="{$linkdetail.URL4|escape|trim}">{$linkdetail.TITLE4|escape|trim}</a></li>
						<li><a href="{$linkdetail.URL5|escape|trim}">{$linkdetail.TITLE5|escape|trim}</a></li>
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