{php}
   $p = array();
   for($i = 1; $i <= $this->get_template_vars('difference'); $i++)
   {
      $p[] = ($this->get_template_vars('percent_last') + $i) * 5 - 256;
   }
   $this->assign('p', $p);
{/php}
{strip}

{foreach from=$p item=i}
   <div style="position:absolute; left:50%; top:80px; width:5px; height:15px; margin-left:{$i}px;">
      <img width="5" height="15" src="images/progbar-single.gif" alt="progress" />
   </div>
{/foreach}

<tr class="{cycle values="odd,even"}">
   <td>{$url}{if $VALIDATE_RECPR}<br />{$recpr_url}{/if}</td>
   {if $VALIDATE_LINKS}
      <td><img src="images/valid_{$link_valid}.gif" width="13" height="13" alt="valid" /> {$valid.$link_valid}</td>
   {/if}
   {if $VALIDATE_RECPR}
      <td><img src="images/valid_{if $recpr_valid >0}2{else}0{/if}.gif" width="13" height="13" alt="recipr.valid" /> {if $recpr_valid >0}Found{else}Not found{/if}</td>
   {/if}
   {if $VALIDATE_LINKS}
      <td>{$errstr|escape}</td>
   {/if}
   {if $VALIDATE_RECPR}
      <td>{if $recpr_valid eq -2}{l}No recpr. link{/l}{elseif $recpr_valid eq -1}{l}Page not found{/l}{elseif $recpr_valid eq 0}{l}Link not found{/l}{else}{l}Ok.{/l}{/if}</td>
   {/if}
</tr>
{/strip}