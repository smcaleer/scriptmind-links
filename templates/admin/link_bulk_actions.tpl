{strip}
{*
 Bulk actions form. Shared between dir_links and dir_approve_links
*}
<table border="0" class="formPage">
<tr><th colspan="3"><h2>{l}With selected links{/l}</h2></td></tr>
<tr><th>Move to existing category</th><td width="1"><input type="radio" name="BulkAction" value="category"></td>
    <td class="smallDesc" id="BulkCategoryCell">
      {html_options options=$categs selected=$category name="BulkCategory" id="BulkCategory"}
   </td></tr>
<tr><th>Move to new subcategory</th><td><input type="radio" name="BulkAction" value="newcategory"></td>
    <td class="smallDesc" id="BulkNewCategoryCell">
        <input type="text" name="BulkNewCategory" id="BulkNewCategory" disabled="disabled"/>
   </td></tr>
<tr><th>Status</th><td><input type="radio" name="BulkAction" value="status" id="BulkStatus"></td>
    <td id="BulkStatusCell">
  			{foreach from=$stats item=v key=k}
  			{if $k ne 1}
                <p><input type="radio" name="BulkStatus" value="{$k}" id="BulkStatus{$k}">
  				<img src="images/stat_{$k}.gif" width="9" height="9" border="0"/> {$stats[$k]}</p>
  			{/if}
  			{/foreach}
</td></tr>
<tr><th>Delete</th><td><input type="radio" name="BulkAction" value="delete"></td><td>Permanently delete the selected links. There is no undo.</td></tr>
<tr><th>No Action</th><td><input type="radio" name="BulkAction" value="skip" checked="checked"></td><td></td></tr>
<tr><th>Confirm</th><td><input type="checkbox" name="BulkConfirm" value="Confirmed" id="BulkConfirm"/></td>
    <td id="BulkGoCell"><input type="submit" name="submit" value="Go" id="BulkGo" disabled="disabled"/></td></tr>
</table>
{/strip}
<script type="text/javascript">
$(document).ready(function(){
    $('#BulkCategoryCell').mouseover(function() {
        var buttonChecked = $('input:radio[name=BulkAction]:nth(0)').prop('checked');
        var thisDisabled = $('#BulkCategory').prop('disabled');
        if( buttonChecked === thisDisabled ) {
            $('#BulkCategory').prop('disabled', !thisDisabled);
        }
    });

    $('#BulkNewCategoryCell').mouseover(function() {
        var buttonChecked = $('input:radio[name=BulkAction]:nth(1)').prop('checked');
        var thisDisabled = $('#BulkNewCategory').prop('disabled');
        if( buttonChecked === thisDisabled ) {
            $('#BulkNewCategory').prop('disabled', !thisDisabled);
        }
    });

    $('#BulkStatusCell').mouseover(function() {
        var buttonChecked = $('input:radio[name=BulkAction]:nth(2)').prop('checked');
        var thisDisabled = $('input:radio[name=BulkStatus]:nth(0)').prop('disabled');
        if( buttonChecked === thisDisabled ) {
            {foreach from=$stats item=v key=k}
                {if $k ne 1}
                    $('#BulkStatus{$k}').prop('disabled', !thisDisabled);
                {/if}
            {/foreach}
        }
    });
    $('#BulkConfirm').click(function() {
        var BulkConfirmedIsChecked = $(this).prop('checked');
        $('#BulkGo').prop('disabled', !BulkConfirmedIsChecked);
    });
    $('#BulkGoCell').mouseover(function() {
        var BulkConfirmedIsChecked = $('#BulkConfirm').prop('checked');
        var thisChecked = $('#BulkGo').prop('disabled');
        if( BulkConfirmedIsChecked === thisChecked ) {
            $('#BulkGo').prop('disabled', !BulkConfirmedIsChecked);
        }
    });
});
</script>