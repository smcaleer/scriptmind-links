   {* Error and confirmation messages *}
   {include file="../messages.tpl"}

{strip}
<div class="{if $update_available ne 1}download{else}warning{/if}">{$version|escape|trim}</div>

{foreach from=$security_warnings item=warning}
   <div class="warning">{$warning|trim}</div>
{/foreach}

<table border="0" cellpadding="0" cellspacing="0" class="list">
  <tr>
     <th>{l}Statistic{/l}</th>
     <th>{l}Value{/l}</th>
  </tr>
  <tr class="odd">
     <td><a href="dir_links.php?r=1">{l}Active Links{/l}</a></td>
     <td>{$stats[0]}</td>
  </tr>
  <tr class="even">
     <td><a href="dir_approve_links.php?r=1">{l}Pending Links{/l}</a></td>
     <td>{$stats[1]}</td>
  </tr>
  <tr class="odd">
     <td>{l}Inactive Links{/l}</td>
     <td>{$stats[2]}</td>
  </tr>
  <tr class="even">
     <td><a href="dir_categs.php?r=1">{l}Categories{/l}</a></td>
     <td>{$stats[3]}</td>
  </tr>
  <tr class="odd">
     <td><a href="email_sent_view.php?r=1">{l}Sent Emails{/l}</a></td>
     <td>{$stats[4]}</td>
  </tr>
  <tr class="even">
     <td><a href="email_message.php?r=1">{l}Email Templates{/l}</a></td>
     <td>{$stats[5]}</td>
  </tr>
</table>

<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase=http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="960" height="200" id="Column3D" >
    <param name="movie" value="{$smarty.const.ADMIN_TEMPLATE_DIR}/swf/FCF_Line.swf" />
    <param name="FlashVars" value="&dataURL=charts.php?type=link-submits&chartWidth=960&chartHeight=200">
    <param name="quality" value="high" />
    <embed src="{$smarty.const.ADMIN_TEMPLATE_DIR}/swf/FCF_Line.swf" flashVars="&dataURL=charts.php?type=link-submits&chartWidth=960&chartHeight=200" quality="high" width="960" height="200" name="Line" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>

<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase=http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="960" height="200" id="Column3D" >
    <param name="movie" value="{$smarty.const.ADMIN_TEMPLATE_DIR}/swf/FCF_Line.swf" />
    <param name="FlashVars" value="&dataURL=charts.php?type=link-sales&chartWidth=960&chartHeight=200">
    <param name="quality" value="high" />
    <embed src="{$smarty.const.ADMIN_TEMPLATE_DIR}/swf/FCF_Line.swf" flashVars="&dataURL=charts.php?type=link-sales&chartWidth=960&chartHeight=200" quality="high" width="960" height="200" name="Line" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>

{foreach from=$news item=item}
<table border="0" cellpadding="0" cellspacing="0" class="news">
   <tr>
      <th><span class="date">{$item.date}</span>{$item.title|escape|trim}</th>
   </tr>
   <tr>
      <td class="body">{$item.body|trim}</td>
   </tr>
</table>
{/foreach}
{/strip}