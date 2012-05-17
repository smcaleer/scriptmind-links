<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   {* Document/Browser title *}
   <title>{$smarty.const.DIRECTORY_TITLE}{$smarty.capture.title|strip}</title>

   {* Meta Tags *}
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
   <meta name="description" content="{$metaDescription}" />
   <meta name="keywords" content="{$metaKeywords}" />
   <meta name="copyright" content="{$metaCopyright}" />
   <meta name="robots" content="INDEX,FOLLOW" />
   <meta name="generator" content="gplLD {$smarty.const.VERSION}" />
   
   {* CSS Style file *}
   <link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/style.css" />
   
</head>
<body>
<div id="body-container">
	<div id="header-container">
		<div id="header">
			{* Error and confirmation messages *}
			{include file="admin/messages.tpl"}

			<h1 id="site-title">
				{$in_page_title|escape|trim}
			</h1>
			<h2 id="site-description">
				{$description|escape|trim}
			</h2>
		</div> <!-- #header -->
	</div> <!-- #header-container -->