{strip}
{if not $start}
{if $error eq 1}<span class="errForm">{l}Please select at least one action to perform.{/l}</span>{/if}
<form method="post" action="">
<table border="0" class="formPage">
  <tr>
  	<th><span class='req'>*</span>{l}Category{/l}:</th>
  	<td class="smallDesc">
  		{html_options options=$categs selected=$CATEGORY_ID name="CATEGORY_ID"}
  	</td>
  </tr>
<tr><th colspan="2">{l}Links{/l}</th></tr>
  <tr>
  	<th><span class='req'>*</span>{l}Check Links{/l}:</th>
  	<td class="smallDesc">
  		<input type="checkbox" name="VALIDATE_LINKS" value="1" {if $VALIDATE_LINKS}checked="1"{/if} />
  	</td>
  </tr>

  <tr>
  	<th>{l}Set inactive if broken{/l}:</th>
  	<td class="smallDesc">
  		{html_checkboxes name="IL" options=$stat_inactive selected=$IL separator=" "}
  	</td>
  </tr>
  <tr>
  	<th>{l}Set active if valid{/l}:</th>
  	<td class="smallDesc">
  		{html_checkboxes name="AL" options=$stat_active selected=$AL separator=" "}
  	</td>
  </tr>

<tr><th colspan="2">{l}Reciprocal Links{/l}</th></tr>
  <tr>
  	<th><span class='req'>*</span>{l}Check Reciprocal Links{/l}:</th>
  	<td class="smallDesc">
  		<input type="checkbox" name="VALIDATE_RECPR" value="1" {if $VALIDATE_RECPR}checked="1"{/if} />
  	</td>
  </tr>
  <tr>
  	<th>{l}Set inactive if broken{/l}:<br />{l}(only if recpr. required){/l}</th>
  	<td class="smallDesc">
  		{html_checkboxes name="IR" options=$stat_inactive selected=$IR separator=" "}
  	</td>
  </tr>
  <tr>
  	<th>{l}Set active if valid{/l}:<br />{l}(only if link not broken){/l}</th>
  	<td class="smallDesc">
  		{html_checkboxes name="AR" options=$stat_active selected=$AR separator=" "}
  	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="submit" value="Start" class="btn"/></td>
  </tr>
</table>
</form>
{else}
<div class="progbar">
</div>
<div class="progspacer">
</div>

<table border="0" cellpadding="0" cellspacing="0" class="list" style="margin-top:20px">
   <tr>
   {foreach from=$columns key=col item=name name=cols}
      <td class="listHeader" id="{$col}">
         {if not $smarty.foreach.cols.last}<img src="images/th_rb.gif" class="rb" alt="" />{/if}
         {$name|escape|trim}
      </td>
   {/foreach}
   </tr>
<!--Progressbar-->
</table>
<br /><br /><br /><br /><br /><br />

{/if}
{/strip}