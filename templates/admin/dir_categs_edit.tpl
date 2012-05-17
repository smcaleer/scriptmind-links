{strip}
{include file="admin/messages.tpl"}
{if $sql_error}
<div class="warning">
<image src="images/no_22.gif"/>
<p>{l}An error occured while saving.{/l}</p>
<p>{l}The database server returned the following message:{/l}</p>
<p>{$sql_error}</p>
</div>
{/if}
{if not $id}
{if $posted}
<div class="alert">
{l}Category saved.{/l}
</div>
{/if}
<form method="post" action="">
<table border="0" class="formPage">
{if $posted}
  <tr><td colspan="2"><h2>Create new {if $symbolic eq 1}symbolic{/if} category</h2></td></tr>
{/if}
  <tr>
  	<th>{if $symbolic ne 1}<span class='req'>*</span>{/if}{l}Title{/l}:</th>
  	<td class="smallDesc">
  		<input type="text" name="TITLE" value="{$TITLE}" size="20" maxlength="100" class="text"/>{if $symbolic ne 1}{validate form="dir_categs_edit" id="v_TITLE" message=$smarty.capture.field_char_required}
  		{validate form="dir_categs_edit" id="v_TITLE_U" message=$smarty.capture.title_not_unique}{/if}
  	</td>
  </tr>
{if $symbolic eq 1}
  <tr>
  	<th>&nbsp</th>
  	<td class="smallDesc">
		<p class="small">{l}Leave blank to follow the title of the category<br />that you're creating a symbolic link for{/l}</p>
  	</td>
  </tr>
{/if}
{if $ENABLE_REWRITE and $symbolic ne 1}
  <tr>
    <th><span class='req'>*</span>{l}URL Title{/l}:</th>
    <td class="smallDesc">
		<input type="text" name="TITLE_URL" value="{$TITLE_URL}" size="20" maxlength="100" class="text"/>{validate form="dir_categs_edit" id="v_TITLE_URL"  message=$smarty.capture.invalid_url_path}
		{validate form="dir_categs_edit" id="v_TITLE_URL_U" message=$smarty.capture.url_title_not_unique}
	</td>
  </tr>
{/if}
{if $symbolic ne 1}
  <tr>
  	<th>{l}Description{/l}:</th>
  	<td class="smallDesc">
  		<textarea name="DESCRIPTION" rows="3" cols="30" class="text">{$DESCRIPTION}</textarea>
  	</td>
  </tr>
{/if}
  <tr>
  	<th><span class='req'>*</span>{l}Parent{/l}:</th>
  	<td class="smallDesc">
  		{html_options options=$categs selected=$PARENT_ID name="PARENT_ID"}
  	</td>
  </tr>
{if $symbolic eq 1}
  <tr>
  	<th><span class='req'>*</span>{l}Symbolic category for{/l}:</th>
  	<td class="smallDesc">
  		{html_options options=$categs selected=$SYMBOLIC_ID name="SYMBOLIC_ID"}{validate form="dir_categs_edit" id="v_SYMBOLIC_ID" message=$smarty.capture.no_url_in_top}{validate form="dir_categs_edit" id="v_SYMBOLIC_ID_E" message=$smarty.capture.invalid_symbolic_category}{validate form="dir_categs_edit" id="v_SYMBOLIC_ID_U" message=$smarty.capture.symbolic_category_exist}{validate form="dir_categs_edit" id="v_SYMBOLIC_ID_P" message=$smarty.capture.invalid_symbolic_parent}
  	</td>
  </tr>
{/if}
  <tr>
  	<th><span class='req'>*</span>{l}Status{/l}:</th>
  	<td class="smallDesc">
  		{html_options options=$stats selected=$STATUS name="STATUS"}
  	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="submit" value="Save" class="btn"/></td>
  </tr>
</table>
</form>
{else}
<script>
var warn = false;
var id = {$id};
</script>

<form method="post" action="dir_categs_edit.php" name="delete">
<table border="0" class="formPage">
  <tr><td colspan="2">{l}The category contains {$count_categs} subcategorie(s) and {$count_links} link(s).<br /> Cannot proceed with delete until further action is taken:{/l}</td></tr>
  <tr>
  	<th>{l}Move all to{/l} <input type="radio" name="DO" value="M" {if $DO eq "M"}checked="1"{/if} onclick="warn=false;return true;" /></th>
  	<td class="smallDesc">
  		{html_options options=$categs selected=$CATEGORY_ID name="CATEGORY_ID"}
  		{if $error}
  		<span class="errForm">{l}Please select another category.{/l}</span>
  		{/if}
  	</td>
  </tr>
  <tr>
  	<th>{l}Delete all{/l} <input type="radio" name="DO" value="D" {if $DO eq "D"}checked="1"{/if} onclick="warn=true;return true;" /></th>
  	<td class="smallDesc">
  		&nbsp;
  	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="hidden" name="action" value="" class="btn"/><input type="submit" name="submit" value="Delete" class="btn" onclick="
{literal}
if(warn){
	if(!confirm('Are you sure you want to delete the category and its entire content?'))
		return false;
}
{/literal}
document.forms['delete']['action'].value='D:{$id}';
return true;
"/> <input type="submit" name="submit" value="Cancel" class="btn"  onclick="document.forms['delete']['action'].value='C:{$id}';return true;"/></td>
  </tr>
</table>
</form>
{/if}
{/strip}