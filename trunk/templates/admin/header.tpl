<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <title>gplLD v{$smarty.const.CURRENT_VERSION} Admin{if !empty($title)} - {$title|escape|trim}{/if}</title>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
     
   {* CSS Style file *}
   <link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/../templates/admin/style.css" />
   <link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/../templates/admin/jqtransform/jqtransform.css" />
   {* jQuery & JS File *}
   <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" type="text/javascript"></script>
   <script src="{$smarty.const.DOC_ROOT}/../templates/admin/jqtransform/jquery.jqtransform.js" type="text/javascript"></script>
   <script src="{$smarty.const.DOC_ROOT}/../templates/admin/js/script.js" type="text/javascript"></script>
</head>
<body>
<div id="body-container">
<div id="navigation">
	<div class="">
		{include file="admin/navigation.tpl"}
	</div>
</div>
<div id="header-container">
   <div id="header">
	  <h1 id="site-title">gplLD Administration Panel</h1>
   {if !empty($title)}
      <h2 id="site-description">{$title|escape|trim}</h2>
   {/if}
   </div>
</div>