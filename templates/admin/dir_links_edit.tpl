   {* Error and confirmation messages *}
   {include file="admin/messages.tpl"}

{strip}
{if $sql_error}
   <div class="warning">
      <img src="images/no_22.gif" alt="" />
      <p>{l}An error occured while saving.{/l}</p>
      <p>{l}The database server returned the following message:{/l}</p>
      <p>{$sql_error}</p>
   </div>
{/if}

{if $posted}
   <div class="alert">
      {l}Link saved.{/l}
   </div>
{/if}

{if isset($AllowedFeat) and $AllowedFeat ne 1}
   <div class="warning">
      <img src="images/no_22.gif" alt="" />
      <p>{l}Maximum number of featured links for this category exceeded!{/l}</p>
      <p>{l}Please review link preferences.{/l}</p>
   </div>
{/if}

<form method="post" action="">
<table border="0" class="formPage">
{if $posted}
  <tr><td colspan="2"><h2>{l}Create new link{/l}</h2></td></tr>
{/if}
  <tr>
   <th><span class='req'>*</span>{l}Title{/l}:</th>
   <td class="smallDesc">
      <input type="text" name="TITLE" value="{$TITLE|escape|trim}" size="30" maxlength="255" class="text" />
      {validate form="dir_links_edit" id="v_TITLE" message=$smarty.capture.field_char_required}
      {validate form="dir_links_edit" id="v_TITLE_U" message=$smarty.capture.title_not_unique}
   </td>
  </tr>
  <tr>
   <th><span class='req'>*</span>{l}URL{/l}:</th>
   <td class="smallDesc">
      <input type="text" name="URL" value="{$URL|escape|trim}" size="40" maxlength="255" class="text"/>
      {validate form="dir_links_edit" id="v_URL" message=$smarty.capture.invalid_url}
      {validate form="dir_links_edit" id="v_URL_U" message=$smarty.capture.url_not_unique}
   </td>
  </tr>
  <tr>
   <th>{l}Description{/l}:</th>
   <td class="smallDesc">
      <textarea name="DESCRIPTION" rows="6" cols="50" class="text" wrap="yes">{$DESCRIPTION|trim|escape}</textarea>
   </td>
  </tr>
  {* For Deeplinks *}
   <tr>
      <th>{l}Title - Link{/l} 1 : </th>
      <td class="smallDesc">
         <input type="text" name="TITLE1" value="{$TITLE1|escape|trim}" size="40" maxlength="255" class="text" />
      </td>
   </tr>
   <tr>
      <th>{l}URL - Link{/l} 1 : </th>
      <td class="field">
         <input type="text" name="URL1" value="{$URL1|escape|trim}" size="40" maxlength="255" class="text"/>
         {validate form="dir_links_edit" id="v_DEEPLINK_URL1" message=$smarty.capture.invalid_url}
      </td>
   </tr>
    <tr>
      <th>{l}Description - Link{/l} 1 : </th>
      <td class="field">
         <textarea name="DESCRIPTION1" rows="3" cols="37" class="text">{$DESCRIPTION1|escape|trim}</textarea>
      </td>
   </tr>
   
   <tr>
      <th>{l}Title - Link{/l} 2 : </th>
      <td class="smallDesc">
         <input type="text" name="TITLE2" value="{$TITLE2|escape|trim}" size="40" maxlength="255" class="text" />
      </td>
   </tr>
   <tr>
      <th>{l}URL - Link{/l} 2 : </th>
      <td class="field">
         <input type="text" name="URL2" value="{$URL2|escape|trim}" size="40" maxlength="255" class="text"/>
         {validate form="dir_links_edit" id="v_DEEPLINK_URL2" message=$smarty.capture.invalid_url}
      </td>
   </tr>
    <tr>
      <th>{l}Description - Link{/l} 2 : </th>
      <td class="field">
         <textarea name="DESCRIPTION2" rows="3" cols="37" class="text">{$DESCRIPTION2|escape|trim}</textarea>
      </td>
   </tr>
   
      <tr>
      <th>{l}Title - Link{/l} 3 : </th>
      <td class="smallDesc">
         <input type="text" name="TITLE3" value="{$TITLE3|escape|trim}" size="40" maxlength="255" class="text" />
      </td>
   </tr>
   <tr>
      <th>{l}URL - Link{/l} 3 : </th>
      <td class="field">
         <input type="text" name="URL3" value="{$URL3|escape|trim}" size="40" maxlength="255" class="text"/>
         {validate form="dir_links_edit" id="v_DEEPLINK_URL3" message=$smarty.capture.invalid_url}
      </td>
   </tr>
    <tr>
      <th>{l}Description - Link{/l} 3 : </th>
      <td class="field">
         <textarea name="DESCRIPTION3" rows="3" cols="37" class="text">{$DESCRIPTION3|escape|trim}</textarea>
      </td>
   </tr>
   
   <tr>
      <th>{l}Title - Link{/l} 4 : </th>
      <td class="smallDesc">
         <input type="text" name="TITLE4" value="{$TITLE4|escape|trim}" size="40" maxlength="255" class="text" />
      </td>
   </tr>
   <tr>
      <th>{l}URL - Link{/l} 4 : </th>
      <td class="field">
         <input type="text" name="URL4" value="{$URL4|escape|trim}" size="40" maxlength="255" class="text"/>
         {validate form="dir_links_edit" id="v_DEEPLINK_URL4" message=$smarty.capture.invalid_url}
      </td>
   </tr>
    <tr>
      <th>{l}Description - Link{/l} 4 : </th>
      <td class="field">
         <textarea name="DESCRIPTION4" rows="3" cols="37" class="text">{$DESCRIPTION4|escape|trim}</textarea>
      </td>
   </tr>
   
      <tr>
      <th>{l}Title - Link{/l} 5 : </th>
      <td class="smallDesc">
         <input type="text" name="TITLE5" value="{$TITLE5|escape|trim}" size="40" maxlength="255" class="text" />
      </td>
   </tr>
   <tr>
      <th>{l}URL - Link{/l} 5 : </th>
      <td class="field">
         <input type="text" name="URL5" value="{$URL5|escape|trim}" size="40" maxlength="255" class="text"/>
         {validate form="dir_links_edit" id="v_DEEPLINK_URL5" message=$smarty.capture.invalid_url}
      </td>
   </tr>
    <tr>
      <th>{l}Description - Link{/l} 5 : </th>
      <td class="field">
         <textarea name="DESCRIPTION5" rows="3" cols="37" class="text">{$DESCRIPTION5|escape|trim}</textarea>
      </td>
   </tr>
   
   <tr>
      <th>{l}Owner Name{/l}:</th>
      <td class="smallDesc">
         <input type="text" name="OWNER_NAME" value="{$OWNER_NAME|escape|trim}" size="30" maxlength="255" class="text" />
      </td>
   </tr>
   <tr>
      <th>{l}Owner Email{/l}:</th>
      <td class="smallDesc">
         <input type="text" name="OWNER_EMAIL" value="{$OWNER_EMAIL|escape|trim}" size="30" maxlength="255" class="text" />
         {validate form="dir_links_edit" id="v_OWNER_EMAIL" message=$smarty.capture.invalid_email}
      </td>
   </tr>
  <tr>
   <th><span class='req'>*</span>{l}Category{/l}:</th>
   <td class="smallDesc">
      {html_options options=$categs selected=$CATEGORY_ID name="CATEGORY_ID"}
      {validate form="dir_links_edit" id="v_CATEGORY_ID" message=$smarty.capture.no_url_in_top}
   </td>
  </tr>
   {if $smarty.const.FTR_ENABLE}
   <tr>
      <th><span class='req'>*</span>{l}Featured{/l}:</th>
      <td class="smallDesc">
         <input type="checkbox" name="FEATURED" value="1" {if $FEATURED}checked="checked"{/if} />
      </td>
   </tr>
   {/if}
  <tr>
   <th><span class='req'>*</span>{l}NoFollow{/l}:</th>
   <td class="smallDesc">
   <input type="checkbox" name="NOFOLLOW" value="1" {if $NOFOLLOW}checked="checked"{/if} />
   </td>
  </tr>
  <tr>
   <th><span class='req'>*</span>{l}Require Reciprocal Link{/l}:</th>
   <td class="smallDesc">
      <input type="checkbox" name="RECPR_REQUIRED" value="1" {if $RECPR_REQUIRED}checked="checked"{/if} />
   </td>
  </tr>
  <tr>
   <th>{l}Reciprocal Link URL{/l}:</th>
   <td class="smallDesc">
      <input type="text" name="RECPR_URL" value="{$RECPR_URL|escape|trim}" size="40" class="text" />
      {validate form="dir_links_edit" id="v_RECPR_URL" message=$smarty.capture.invalid_url}
      {if !empty ($RECPR_URL)}
         <p><a style="margin:1px 1em;" href="{$RECPR_URL|escape|trim}" title="{l}Visit{/l}: {$RECPR_URL|escape|trim}" class="btn" target="_blank">{l}Visit reciprocal page{/l}</a></p>
      {/if}
   </td>
  </tr>
  <tr>
   <th><span class='req'>*</span>{l}Status{/l}:</th>
   <td class="smallDesc">
      {html_options options=$stats selected=$STATUS name="STATUS"}
   </td>
  </tr>
  <tr>
   <th>{l}Link Expires{/l}:</th>
   <td class="smallDesc">
      <input type="text" name="EXPIRY_DATE" value="{$EXPIRY_DATE}" size="20" maxlength="40" class="text"/>
      {validate form="dir_links_edit" id="v_EXPIRY_DATE" message=$smarty.capture.invalid_date}
   </td>
  </tr>

  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="submit" value="Save" class="btn" /></td>
  </tr>
</table>
</form>
{/strip}