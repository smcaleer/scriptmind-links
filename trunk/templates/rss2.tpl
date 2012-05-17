<?xml version="1.0" encoding="UTF-8"?>
{strip}
<rss version="2.0">
 <channel>
      <title>{$smarty.const.DIRECTORY_TITLE}{$title|trim}</title>
      <link>{$url}</link>
      <description>{$smarty.const.SITE_DESC} {$description|trim}</description>
      {foreach from=$links item=link}
         <item>
            <title>{$link.TITLE|trim}</title>
            <link>{$link.URL|trim}</link>
            {if $link.DESCRIPTION}
            <description>{$link.DESCRIPTION|trim}</description>
            {/if}
            <pubDate>{$link.DATE_ADDED|date_format:"%a, %d %b %Y %H:%M:%S GMT"}</pubDate>
           </item>
        {/foreach}
   </channel>
</rss>
{/strip}