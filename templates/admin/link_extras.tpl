{strip}
<div class="tt" id="tT{$id}">
   <table class="ttt" border="0" cellpadding="0" cellspacing="0">
   <tbody>
      {foreach from=$extra_fields key=col item=name}
        <tr><td>{l}{$name}{/l}:</td><td> {$row.$col}&nbsp;</td></tr>
      {/foreach}
   </tbody>
   </table>
</div>
{/strip}