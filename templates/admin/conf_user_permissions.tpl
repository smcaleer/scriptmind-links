   {* Error and confirmation messages *}
   {include file="admin/messages.tpl"}

{strip}
{if $sql_error}
   <div class="warning">
      <img src="images/no_22.gif" alt="" />
      <p>{l}An error occured while saving.{/l}</p>
      <p>{l}The database server returned the following message:{/l}</p>
      <p>{$sql_error|escape}</p>
   </div>
{/if}

{if $posted}
   <div class="alert">
      {l}{$posted|escape}{/l}
   </div>
{/if}

{if $WARN}
<form method="post" action="conf_user_permissions.php" name="delete">
<table border="0" class="formPage">
  <tr>
   <td>
      <h2>Permissions for {$user_detail.LOGIN} - {$user_detail.NAME}</h2>
   </td>
  </tr>
  <tr>
   <td class="smallDesc">
      Category {$CATEGORY} is parent to {$CHILD_CATEGORIES} {if $CHILD_CATEGORIES eq 0}category{else}categories{/if} that this user has permission to.<BR>
      Proceed to grant permission to category {$CATEGORY} and delete the existing permission to the {$CHILD_CATEGORIES} {if $CHILD_CATEGORIES eq 0}category{else}categories{/if}?
   </td>
  </tr>
  <tr>
   <td>
      <input type="hidden" name="action" value="" class="btn"/>
      <input type="submit" name="submit" value="Proceed" class="btn" onclick="document.forms['delete']['action'].value='A:{$CATEGORY_ID}';return true;"/> <input type="submit" name="submit" value="Cancel" class="btn"  onclick="document.forms['delete']['action'].value='C:{$CATEGORY_ID}';return true;"/>
   </td>
  </tr>

</table>
</form>
{else}
<form method="post" action="conf_user_permissions.php?action=N">
<table border="0" class="formPage">
  <tr>
   <td colspan="2">
      <h2>Permissions for {$user_detail.LOGIN} - {$user_detail.NAME}</h2>
   </td>
  </tr>
  <tr>
   <td class="label">{l}Category{/l}:</td>
   <td class="smallDesc">
      {* Load category selection *}
      {html_options options=$categs selected=$CATEGORY_ID name="CATEGORY_ID"}
      {validate form="conf_user_permissions" id="v_CATEGORY_ID" message=$smarty.capture.no_url_in_top}
      {validate form="conf_user_permissions" id="v_CATEGORY_ID_U" message=$smarty.capture.permission_not_unique}
      {validate form="conf_user_permissions" id="v_CATEGORY_ID_S" message=$smarty.capture.permission_is_sub_cat}
      &nbsp;<input type="submit" name="submit" value="Add Permission" class="btn"/>
   </td>
  </tr>

</table>
</form>
{/if}

<table border="0" cellpadding="0" cellspacing="0" class="list">
   <tr>
   {foreach from=$columns key=col item=name}
      {if $ENABLE_REWRITE or $col ne 'TITLE_URL'}
         <td class="listHeader" id="{$col}"><img src="images/th_rb.gif" class="rb" alt="" />
         {if $SORT_FIELD eq $col}
            {if $SORT_ORDER eq 'ASC'}
               <img src="images/sort_a.gif" width="16" height="9" class="order" alt="ascending" />
            {else}
               <img src="images/sort_d.gif" width="16" height="9" class="order" alt="descending" />
            {/if}
         {else}
            <img src="images/spacer.gif" width="16" height="9" class="order" alt="" />
         {/if}
         {$name|escape}
         </td>
      {/if}
   {/foreach}
   <td class="listHeader">{l}Action{/l}</td>
   </tr>
   {foreach from=$list item=row key=id}
   <tr class="{cycle values="odd,even"}">
      {foreach from=$columns key=col item=name}
         {if $ENABLE_REWRITE or $col ne 'TITLE_URL'}
            <td>
               {if $col eq 'CATEGORY_PATH'}
                  {foreach from=$row.$col item=category name=path}
                     {if $smarty.foreach.path.iteration gt 2} &gt; {/if}
                     {if not $smarty.foreach.path.first}
                        {$category.TITLE|escape}
                     {/if}
                  {/foreach}
               {else}
                  {$row.$col|escape}&nbsp;
               {/if}
            </td>
         {/if}
      {/foreach}
      <td align="center"><a href="conf_user_permissions.php?action=D:{$id}" onclick="return link_rm_confirm('{l}Are you sure you want to remove this permission?{/l}');" title="{l}Remove Permission{/l}"><img src="images/a_delete.gif" width="16" height="13" border="0" alt="Delete" /></a></td>
   </tr>
   {foreachelse}
   <tr>
      <td colspan="{if $ENABLE_REWRITE}9{else}8{/if}" class="norec">{l}No records found.{/l}</td>
   </tr>
   {/foreach}
   <tr>
      <td colspan="{if $ENABLE_REWRITE}9{else}8{/if}" class="norec">{include file="admin/list_pager.tpl"}</td>
   </tr>
</table>
<script type="text/javascript" src="files/table.js"></script>
<script type="text/javascript">
/* <![CDATA[ */
   tableInit();
/* ]]> */
</script>
{/strip}