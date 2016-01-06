<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="{if $pageDescription}{$pageDescription}{else}MOFILM and webtrends want you to be the Video Voice of SXSW. Tell us what is interesting, cool, intriguing, beguiling about any of the three SXSW festivals and get the chance to win a trip for two to the TriBeCa Film festival.{/if}" />
		<meta name="author" content="{$appAuthor}" />
		<meta name="copyright" content="{$appCopyright}" />
		<meta name="robots" content="index, follow" />
		<meta name="keywords" content="{if $pageKeywords}{$pageKeywords}{else}southbytes, films, SXSW, SXSW film, SXSW films, SXSW interactive, competition, competitions, contest, contests{/if}" />
{if $metaRedirect}
		<meta http-equiv="refresh" content="{$metaTimeout|default:10};url={$metaRedirect}" />
{/if}
		<title>{$oRequest->getServerName()} {if $pageTitle}- {$pageTitle}{/if}</title>
		<link rel="stylesheet" type="text/css" href="{$themefolder}/css/style.css" media="screen" />
{*		<!--[if IE 7]>
		<link rel="stylesheet" type="text/css" href="{$themefolder}/css/ie7.css" />
		<![endif]-->*}
{foreach $oView->getResourcesByType('css') as $oResource}
		{$oResource->toString()}
{/foreach}
		<link rel="home" title="Home" href="/" />
		<link rel="shortcut icon" href="{$themeimages}/favicon.png" type="image/png" />
	</head>
