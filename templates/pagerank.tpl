<div class="pr">
   {if $pr ge 0}
      PR: {$pr}
   {else}
      N/A
   {/if}
   <div class="prg">
      <div class="prb" style="width: {if $pr gt -1}{math equation="x*4" x=$pr}{else}0{/if}px"></div>
   </div>
</div>