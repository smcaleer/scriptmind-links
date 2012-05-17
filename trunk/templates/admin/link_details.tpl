{strip}
<div class="tt" id="tT{$id}">
   <table class="ttt" border="0" cellpadding="0" cellspacing="0">
   <tbody>
      <tr><td>{l}Link ID{/l}:</td><td> {$id}</td></tr>
      <tr><td>{l}Title{/l}:</td><td> {$row.TITLE|escape|trim}</td></tr>
      <tr><td>{l}Description{/l}:</td><td> {$row.DESCRIPTION|escape|trim}</td></tr>
      <tr><td>{l}Category{/l}:</td><td> {$row.CATEGORY|escape|trim}</td></tr>

      <tr><td>{l}PageRank{/l}:</td><td> {if $row.PAGERANK eq -1}<em>N/A</em>{else}{$row.PAGERANK|escape|trim}{/if}</td></tr>
      <tr><td>{l}Hits{/l}:</td><td> {$row.HITS}</td></tr>

      <tr><td>{l}Date Added{/l}:</td><td> {$row.DATE_ADDED|date_format:$date_format}</td></tr>
      <tr><td>{l}Valid{/l}:</td><td> {$valid[$row.VALID]} ({$row.LAST_CHECKED|date_format:$date_format})</td></tr>
      <tr><td>{l}Recpr. Link URL{/l}:</td><td> {$row.RECPR_URL|escape|trim}</td></tr>
      <tr><td>{l}Recpr. PageRank{/l}:</td><td> {if $row.RECPR_PAGERANK eq -1}<em>N/A</em>{else}{$row.RECPR_PAGERANK}{/if}</td></tr>
      <tr><td>{l}Recpr. Valid{/l}:</td><td> {$valid[$row.RECPR_VALID]} ({$row.RECPR_LAST_CHECKED|date_format:$date_format})</td></tr>

      <tr><td>{l}Owner Name{/l}:</td><td> {$row.OWNER_NAME|escape|trim}</td></tr>
      <tr><td>{l}Owner Email{/l}:</td><td> {$row.OWNER_EMAIL|escape|trim}</td></tr>
      {if !empty($row.IPADDRESS)}
         <tr><td>{l}Owner IP{/l}:</td><td> {$row.IPADDRESS|trim}</td></tr>
      {/if}
   </tbody>
   </table>
</div>
{/strip}