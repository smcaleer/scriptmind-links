{capture name="title"} - {l}Link Payment{/l}{/capture}
{capture assign="in_page_title"}{l}Link Payment{/l}{/capture}
{capture assign="description"}{l}Submit a new link to the directory{/l}{/capture}

{include file="header.tpl"}
{include file="navigation.tpl"}

{strip}
   {if empty ($ID)}
      {l}Invalid link id.{/l}
   {else}
      <form method="post" action="">
      <table border="0" class="formPage">
      {if $action eq 'payed'}
         <tr>
            <td colspan="2" class="msg">
               {l}Thank you.{/l}
               <br />
               {l}Link submited and awaiting approval.{/l}
            </td>
         </tr>
      {elseif $action eq 'canceled'}
         <tr>
            <td colspan="2" class="err">
               {l}The payment was canceled.{/l}
            </td>
         </tr>
      {/if}
      {if $error}
         <tr>
            <td colspan="2" class="err">
               {l}An error occured while saving the link payment data.{/l}
            </td>
         </tr>
      {/if}
      <tr>
        <td class="label">{l}Title{/l}:</td>
        <td class="field">{$TITLE|escape|trim}</td>
      </tr>

      <tr>
        <td class="label">{l}URL{/l}:</td>
        <td class="field">{$URL|escape|trim}</td>
      </tr>

      <tr>
        <td class="label">{l}Description{/l}:</td>
        <td class="field">{$DESCRIPTION|escape|trim}</td>
      </tr>

      <tr>
        <td class="label">{l}Your Name{/l}:</td>
        <td class="field">{$OWNER_NAME|escape|trim}</td>
      </tr>

      <tr>
         <td class="label">{l}Your Email{/l}:</td>
         <td class="field">{$OWNER_EMAIL|escape:decentity|trim}</td>
      </tr>

      <tr>
         <td class="label">{l}Unit price{/l}</td>
         {assign var="um" value="`$smarty.const.PAY_UM`"}
         <td class="field">$ {$price.$LINK_TYPE}/{$payment_um.$um}</td>
      </tr>

      {if $action eq 'pay'}
      <tr>
         <td class="label">{l}Quantity{/l}</td>
         <td class="field">
            {if $smarty.const.PAY_UM ne 5}
               <input type="text" name="quantity" value="{$quantity}" size="10" maxlength="5" class="text" />
            {else}
               {l}n/a{/l}<input type="hidden" name="quantity" value="1" />
            {/if}
            {validate form="pay_link" id="v_quantity" message=$smarty.capture.field_integer_required}
         </td>
      </tr>

      <tr>
         <td class="buttons" colspan="2" align="center">
            <input type="hidden" name="submit" value="1" />
            <input type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/x-click-but01.gif" alt="Make payments with PayPal - it's fast, free and secure!"/>
         </td>
      </tr>
      {/if}
      </table>
      </form>
   {/if}
   {include file="footer.tpl"}
{/strip}