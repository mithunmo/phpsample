<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="Mofilm Admin System" />
		<meta name="author" content="{$appAuthor}" />
		<meta name="copyright" content="{$appCopyright}" />
		<meta name="robots" content="noindex, nofollow" />
		<title>{$oRequest->getServerName()} {if $pageTitle}- {$pageTitle}{/if}</title>
		<link rel="stylesheet" type="text/css" href="{$themefolder}/css/global.css?{mofilmConstants::CSS_VERSION}" media="screen" />
		<link rel="stylesheet" type="text/css" href="{$themefolder}/css/admin.css?{mofilmConstants::CSS_VERSION}" media="screen" />
		<link rel="stylesheet" type="text/css" href="/libraries/jquery-ui/themes/smoothness/jquery-ui.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="{$themefolder}/css/thickbox.css" media="screen" />
{foreach $oView->getResourcesByType('css') as $oResource}
		{$oResource->toString()}
{/foreach}
		<link rel="home" title="Home" href="/" />
		<link rel="shortcut icon" href="/favicon.ico" />
	</head>
