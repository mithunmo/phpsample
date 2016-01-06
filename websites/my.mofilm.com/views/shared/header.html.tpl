<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="My Mofilm.com {if $pageDescription}{$pageDescription}{/if}" />
		<meta name="author" content="{$appAuthor}" />
		<meta name="copyright" content="{$appCopyright}" />
		<meta name="google-site-verification" content="F8IKQqxsjr_bm8JkmCEpotXG7FK4kYsDZ2Cxy68-eZ8" />
		{if $metaRedirect}<meta http-equiv="refresh" content="{$metaTimeout|default:10};url={$metaRedirect}" />{/if}
		<title>{$oRequest->getServerName()} {if $pageTitle}- {$pageTitle}{/if}</title>
		<link rel="stylesheet" type="text/css" href="{$themefolder}/css/global.css?{mofilmConstants::CSS_VERSION}" media="screen" />
		<link rel="stylesheet" type="text/css" href="{$themefolder}/css/my.css?{mofilmConstants::CSS_VERSION}" media="screen" />
		<link rel="stylesheet" type="text/css" href="/libraries/jquery-ui/themes/smoothness/jquery-ui.css" media="screen" />
		<!--[if IE 7]>
		<link rel="stylesheet" type="text/css" href="{$themefolder}/css/ie7.css?{mofilmConstants::CSS_VERSION}" />
		<![endif]-->
                <!---test -->
{foreach $oView->getResourcesByType('css') as $oResource}
		{$oResource->toString()}
{/foreach}
		<link rel="shortcut icon" href="/favicon.ico" />
	</head>
