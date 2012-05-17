<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <title>gplLD v{$smarty.const.CURRENT_VERSION} Admin - Login</title>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <meta name="robots" content="noindex, nofollow" />
   
   {* CSS Style file *}
   <link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/../templates/admin/style.css" />
   <link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/../templates/admin/jqtransform/jqtransform.css" />
   {* jQuery & JS File *}
   <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" type="text/javascript"></script>
   <script src="{$smarty.const.DOC_ROOT}/../templates/admin/jqtransform/jquery.jqtransform.js" type="text/javascript"></script>
   <script src="{$smarty.const.DOC_ROOT}/../templates/admin/js/script.js" type="text/javascript"></script>
</head>
<body style="vertical-align:middle">
{* Error and confirmation messages *}
{include file="admin/messages.tpl"}
<div id="body-container">
	<div id="header-container">
		<div id="header">
			<h1 id="site-title">gplLD v{$smarty.const.CURRENT_VERSION} Admin</h1>
			<h2 id="site-description">Login</h2>
		</div>
	</div>
	<div id="container">
		<div id="content">
			<div class="content">
				<form method="post" action="">
			         <table border="0" cellpadding="0" cellspacing="5" style="margin:0 auto; text-align:left;">
			            <tr>
			               <td colspan="2">
			                  {if $failed}
			                     <span class="warning" style="margin:5px;">{l}Invalid username or password.{/l}</span>
			                  {/if}
			                  {if $no_permission}
			                     <span class="warning" style="margin:5px;">{l}No permissions set for this user.{/l}</span>
			                  {/if}&nbsp;
			               </td>
			            </tr>
			            <tr>
			               <td>{l}User{/l}</td>
			               <td>
			                  <input type="text" name="user" value="{$user}" size="10" maxlength="100" class="text" />
			                  {validate form="login" id="v_user" message=$smarty.capture.field_required}
			               </td>
			            </tr>
			            <tr>
			               <td>{l}Password{/l}</td>
			               <td>
			                  <input type="password" name="pass" value="" size="10" maxlength="100" class="text" />
			                  {validate form="login" id="v_pass" message=$smarty.capture.field_required}
			               </td>
			            </tr>
			            <tr>
			               <td>&nbsp;</td>
			               <td><input type="submit" name="submit" value="Login" class="btn" /></td>
			            </tr>
			         </table>
			
				</form>
			</div>
		</div>
	</div>
</div>
</body>
</html>