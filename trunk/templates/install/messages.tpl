{capture name='field_required'}<span class="warning">{l}This field is required{/l}</span>{/capture}
{capture name='invalid_username'}<span class="warning">{l}Invalid username. Please see the field help for more details.{/l}</span>{/capture}
{capture name='invalid_email'}<span class="warning">{l}Invalid email address format{/l}</span>{/capture}
{capture name='invalid_password'}<span class="warning">{l}Invalid password. Please see the field help for more details.{/l}</span>{/capture}
{capture name='password_not_match'}<span class="warning">{l}Password confirmation does not match. Please type again.{/l}</span>{/capture}
{capture name=form_error}{strip}
{if $form_error eq 'INSTALL_ERROR_CONNECT'}
<div class="warning">
<p>{l}An error occured while connecting to the database. Please check your database username and password.{/l}</p>
<p>{l}If you require help with your database server settings, please consult your hosting company.{/l}</p>
<p>{l}The database server returned the following message:{/l}</p>
<p>{$sql_error}</p>
</div>
{elseif $form_error eq 'INSTALL_ERROR_CREATE_DB'}
<div class="warning">
<p>{l}An error occured while creating the database. Please check if you have database create rights.{/l}</p>
<p>{l}If you require help with your database server settings, please consult your hosting company.{/l}</p>
<p>{l}The database server returned the following message:{/l}</p>
<p>{$sql_error}</p>
</div>
{elseif $form_error eq 'INSTALL_ERROR_CREATE'}
<div class="warning">
<p>{l}An error occured while creating/updating the database structure. Please check if you have appropriate database rights.{/l}</p>
<p>{l}If you require help with your database server settings, please consult your hosting company.{/l}</p>
<p>{l}The database server returned the following message:{/l}</p>
<p>{$sql_error}</p>
</div>
{elseif $form_error eq 'SQL_ERROR_ADMIN'}
<div class="warning">
<p>{l}An error occured while creating the administrative user.{/l}</p>
<p>{l}The database server returned the following message:{/l}</p>
<p>{$sql_error}</p>
</div>
{elseif $form_error eq 'ADMIN_REQUIRED'}
<div class="warning">
<p>{l}No administrative user was found in the database.{/l}</p>
<p>{l}You must create an administrative user, otherwise the application would be unusable.{/l}</p>
</div>
{elseif $form_error eq 'CONFIG_NOT_FOUND'}
<div class="warning">
<p>{l}Config file was not found.{/l}</p>
</div>
{elseif $form_error eq 'CONFIG_NOT_WRITABLE'}
<div class="warning">
<p>{l}Config file is not writable.{/l}</p>
</div>
{/if}
{/strip}{/capture}
{if $message ne ''}
{capture name=message}{strip}
<div class="msg">
{if $message eq 'INSTALL_DB_CREATED'}
	<p>{l}Database was created succesfully.{/l}</p>
{elseif $message eq 'INSTALL_DB_UPDATED'}
	<p>{l}Database was updated succesfully.{/l}</p>
{elseif $message eq 'ADMIN_CREATED'}
	<p>{l}Administrative user was created/updated succesfully.{/l}</p>
{/if}
</div>
{/strip}{/capture}
{/if}