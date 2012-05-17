{capture name="title"} - {l}Submit Link{/l}{/capture}
{capture assign="in_page_title"}{l}Submit Link{/l}{/capture}
{capture assign="description"}{l}Submit a new link to the directory{/l}{/capture}

{include file="header.tpl"}
{include file="navigation.tpl"}

<div id="container">
	<div id="content"> <!-- for categories/links -->
	<div class="content">
		{include file="breadcrumb.tpl"}
		
	<form method="post" action="">
		{if $error}
			<div class="notice">
			{l}An error occured while saving the link.{/l}
			{if !empty($sqlError)}
				<p>{$sqlError}</p>
			{/if}
			</div>
		{/if}
	
		{if $posted}
			<div class="download">
			{l}Link submitted and awaiting approval.{/l}<br />
			{l}Submit another link.{/l}
			</div>
		{/if}
	
		<fieldset>
			<legend>Submission Guidelines</legend>
			<ul>
				<li>Submit your link to the most appropriate category.</li>
			</ul>
		</fieldset>
	
		{if count($price) gt 0}
		<fieldset>
			<legend>{l}Pricing{/l}:</legend>
			{if $price.featured_plus}
				<input type="radio" name="LINK_TYPE" value="featured_plus"{if $LINK_TYPE eq 'featured_plus'} checked="true"{/if} />{l}Featured+ links{/l} ${$price.featured_plus}<br/>
			{/if}
			{if $price.featured}
				<input type="radio" name="LINK_TYPE" value="featured"{if $LINK_TYPE eq 'featured'} checked="true"{/if} />{l}Featured links{/l} ${$price.featured}<br/>
			{/if}
			{if $price.normal_plus gt 0}
				<input type="radio" name="LINK_TYPE" value="normal_plus"{if $LINK_TYPE eq 'normal_plus'} checked="true"{/if} />{l}Regular+ links{/l} ${$price.normal_plus}<br/>
			{/if}
			{if $price.normal gt 0}
				<input type="radio" name="LINK_TYPE" value="normal"{if $LINK_TYPE eq 'normal'} checked="true"{/if} />{l}Regular links{/l} ${$price.normal}<br/>
			{elseif $price.normal eq 0}
				<input type="radio" name="LINK_TYPE" value="normal"{if $LINK_TYPE eq 'normal'} checked="true"{/if} />{l}Regular links{/l} {l}free{/l}<br/>
			{/if}
			{if $price.reciprocal gt 0}
				<input type="radio" name="LINK_TYPE" value="reciprocal"{if $LINK_TYPE eq 'reciprocal'} checked="true"{/if} />{l}Regular links with reciprocal{/l} ${$price.reciprocal}<br/>
			{elseif $price.reciprocal eq 0}
				<input type="radio" name="LINK_TYPE" value="reciprocal"{if $LINK_TYPE eq 'reciprocal'} checked="true"{/if} />{l}Regular links with reciprocal{/l} {l}free{/l}<br/>
			{/if}
			{if isset($price.free)}
				<input type="radio" name="LINK_TYPE" value="free"{if $LINK_TYPE eq 'free'} checked="true"{/if} />{l}Links with nofollow attribute{/l} free<br/>
			{/if}
			{validate form="submit_link" id="v_LINK_TYPE" message=$smarty.capture.field_link_type}
		</fieldset>
		{/if}
	
		<label for="title">
			<span class='req'>*</span>{l}Title{/l}:
		</label>
		<input type="text" id="title" name="TITLE" value="{$TITLE|escape|trim}" size="40" maxlength="255" class="text" />
		{validate form="submit_link" id="v_TITLE" message=$smarty.capture.field_char_required}
		{validate form="submit_link" id="v_TITLE_U" message=$smarty.capture.title_not_unique}
	
		<label for="URL">
			<span class='req'>*</span>{l}URL{/l}:
		</label>
	
		<input type="text" id="URL" name="URL" value="{$URL|escape|trim}" size="40" maxlength="255" class="text"/>
		{validate form="submit_link" id="v_URL" message=$smarty.capture.invalid_url}
		{validate form="submit_link" id="v_URL_ONLINE" message=$smarty.capture.url_not_online}
		{validate form="submit_link" id="v_URL_U" message=$smarty.capture.url_not_unique}
	
		{* For Deeplinks *}
		<fieldset>
			<legend>Deep Links</legend>
			<label for="title1">
				{l}Title 1{/l}:
			</label>
			<input type="text" id="title1" name="TITLE1" value="{$TITLE1|escape|trim}" size="40" maxlength="255" class="text" />
	
			<label for="URL1">
				{l}URL 1{/l}:
			</label>
			<input type="text" id="URL1" name="URL1" value="{$URL1|escape|trim}" size="40" maxlength="255" class="text"/>
			{validate form="submit_link" id="v_DEEPLINK_URL1" message=$smarty.capture.invalid_url}
	
	
			<label for="title2">
				{l}Title 2{/l}:
			</label>
			<input type="text" id="title2" name="TITLE2" value="{$TITLE2|escape|trim}" size="40" maxlength="255" class="text" />
	
			<label for="URL2">
				{l}URL 2{/l}:
			</label>
			<input type="text" id="URL2" name="URL2" value="{$URL2|escape|trim}" size="40" maxlength="255" class="text"/>
			{validate form="submit_link" id="v_DEEPLINK_URL2" message=$smarty.capture.invalid_url}
	
	
			<label for="title3">
				{l}Title 3{/l}:
			</label>
			<input type="text" id="title3" name="TITLE3" value="{$TITLE3|escape|trim}" size="40" maxlength="255" class="text" />
	
			<label for="URL3">
				{l}URL 3{/l}:
			</label>
			<input type="text" id="URL3" name="URL3" value="{$URL3|escape|trim}" size="40" maxlength="255" class="text"/>
			{validate form="submit_link" id="v_DEEPLINK_URL3" message=$smarty.capture.invalid_url}
	
	
			<label for="title4">
				{l}Title 4{/l}:
			</label>
			<input type="text" id="title4" name="TITLE4" value="{$TITLE4|escape|trim}" size="40" maxlength="255" class="text" />
	
			<label for="URL4">
				{l}URL 4{/l}:
			</label>
			<input type="text" id="URL4" name="URL4" value="{$URL4|escape|trim}" size="40" maxlength="255" class="text"/>
			{validate form="submit_link" id="v_DEEPLINK_URL4" message=$smarty.capture.invalid_url}
	
	
			<label for="title5">
				{l}Title 5{/l}:
			</label>
			<input type="text" id="title5" name="TITLE5" value="{$TITLE5|escape|trim}" size="40" maxlength="255" class="text" />
	
			<label for="URL5">
				{l}URL 5{/l}:
			</label>
			<input type="text" id="URL5" name="URL5" value="{$URL5|escape|trim}" size="40" maxlength="255" class="text"/>
			{validate form="submit_link" id="v_DEEPLINK_URL5" message=$smarty.capture.invalid_url}
		</fieldset>
		{* End Deeplinks *}
	
		<label for="DESCRIPTION">
			{l}Description{/l}:
		</label>
	
		<textarea id="DESCRIPTION" name="DESCRIPTION" rows="3" cols="37" class="text">{$DESCRIPTION|escape|trim}</textarea>
	
	
		<label for="OWNER_NAME">
			<span class='req'>*</span>{l}Your Name{/l}:
		</label>
		<input type="text" id="OWNER_NAME" name="OWNER_NAME" value="{$OWNER_NAME|escape|trim}" size="40" maxlength="255" class="text" />
		{validate form="submit_link" id="v_OWNER_NAME" message=$smarty.capture.field_char_required}
	
		<label for="OWNER_EMAIL">
			<span class='req'>*</span>{l}Your Email{/l}:
		</label>
		<input type="text" id="OWNER_EMAIL" name="OWNER_EMAIL" value="{$OWNER_EMAIL|escape|trim}" size="40" maxlength="255" class="text" />
		{validate form="submit_link" id="v_OWNER_EMAIL" message=$smarty.capture.invalid_email}
	
		<label for="CATEGORY_ID">
			<span class='req'>*</span>{l}Category{/l}:
		</label>
		{html_options options=$categs selected=$CATEGORY_ID name="CATEGORY_ID" id="CATEGORY_ID"}
		{validate form="submit_link" id="v_CATEGORY_ID" message=$smarty.capture.no_url_in_top}
	
	
	
		<label for="RECPR_URL">{if $recpr_required}<span class='req'>*</span>{/if}{l}Reciprocal Link URL{/l}:</label>
		<input type="text" name="RECPR_URL" id="RECPR_URL" value="{$RECPR_URL|escape|trim}" size="40" maxlength="255" class="text" />
		{validate form="submit_link" id="v_RECPR_URL" message=$smarty.capture.invalid_url}
		{validate form="submit_link" id="v_RECPR_ONLINE" message=$smarty.capture.url_not_online}
		{validate form="submit_link" id="v_RECPR_LINK" message=$smarty.capture.recpr_not_found|replace:'#SITE_URL#':$smarty.const.SITE_URL}
		<br />
		<label for="RECPR_TEXT">
			{l}To validate the reciprocal link please include the following HTML code in the page at the URL specified above, before submiting this form:{/l}
		</label>
		<textarea name="RECPR_TEXT" id="RECPR_TEXT" rows="2" readonly="readonly" cols="37" class="text">&lt;a href="{$smarty.const.SITE_URL}"&gt;{$smarty.const.SITE_NAME}&lt;/a&gt;</textarea>
	
		{if $smarty.const.VISUAL_CONFIRM}
		<div id="reCaptcha">
			<div>
				{if isset($reCaptchaError) && $reCaptchaError ne 1}
				<div class="error">
					{l}Invalid Input{/l}
				</div>
				{/if}
			{recaptcha error=$reCaptchaError}
			</div>
		</div>
		{/if}
	
		<br/>
		<input type="submit" name="submit" value="{l}Continue{/l}" class="submit" />
	</form>
	
</div> <!-- .content -->
	
{include file="sidebar.tpl"}
	
</div> <!-- #content -->
</div> <!-- #container -->
{include file="footer.tpl"}

