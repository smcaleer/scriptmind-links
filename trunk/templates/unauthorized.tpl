{capture name="title"} - {l}Unauthorized{/l}{/capture}
{capture assign="in_page_title"}{l}Unauthorized{/l}{/capture}
{capture assign="description"}{l}No authorization{/l}{/capture}
{include file="header.tpl"}
{include file="top_bar.tpl"}

{strip}
<table border="0" class="formPage" align="center" style="clear:both; margin-top:3em;">
   <tr>
      <td class="err">
         <h2>{l}Unauthorized{/l}</h2>
         <p>{l}Sorry, you are not allowed to access this page.{/l}</p>

         {if isset($unauthorizedReason) and !empty($unauthorizedReason)}
            <h2>{l}Reason{/l}</h2>
            <p>{$unauthorizedReason|escape|trim}</p>
         {/if}
      </td>
   </tr>
</table>

{include file="footer.tpl"}
{/strip}