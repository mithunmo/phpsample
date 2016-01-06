<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="My Mofilm.com {if $pageDescription}{$pageDescription}{/if}" />
		<meta name="author" content="{$appAuthor}" />
		<meta name="copyright" content="{$appCopyright}" />
		<meta name="robots" content="noindex, nofollow" />
		<meta name="google-site-verification" content="F8IKQqxsjr_bm8JkmCEpotXG7FK4kYsDZ2Cxy68-eZ8" />
		{if $metaRedirect}<meta http-equiv="refresh" content="{$metaTimeout|default:10};url={$metaRedirect}" />{/if}
		<title>{$oRequest->getServerName()} {if $pageTitle}- {$pageTitle}{/if}</title>
		<link rel="stylesheet" type="text/css" href="{$themefolder}/css/momusicglobal.css?{mofilmConstants::CSS_VERSION}" media="screen" />
		<link rel="stylesheet" type="text/css" href="{$themefolder}/css/my.css?{mofilmConstants::CSS_VERSION}" media="screen" />
		<link rel="stylesheet" type="text/css" href="/libraries/jquery-ui/themes/smoothness/jquery-ui.css" media="screen" />
		<!--[if IE 7]>
		<link rel="stylesheet" type="text/css" href="{$themefolder}/css/ie7.css?{mofilmConstants::CSS_VERSION}" />
		<![endif]-->
{foreach $oView->getResourcesByType('css') as $oResource}
		{$oResource->toString()}
{/foreach}
		<link rel="shortcut icon" href="/favicon.ico" />
	<style type="text/css">
		html {
			height:400px;
		}
		#moviemasher_container {
			height:400px;
		}

	table.data { width: 80%; margin: auto; }
	table.data.smallText { font-size: 11px; }
	table.data thead tr { background-color: #fff; }
	table.data thead th { border-bottom: 2px solid #000; text-align: left; padding: 3px; }
	table.data thead th.first { width: 80px; }
	table.data thead th.last, table.data tbody td.last { width: 80px; }
	table.data thead th.alignCenter { text-align: center; }
	table.data tbody th { text-align: left; font-weight: bold; }
	table.data tbody tr.alt { background-color: #dedede; }
	table.data tbody tr.selected { background-color: #CCEBFF; }
	table.data tbody td { border-bottom: 1px solid #aaa; padding-left: 3px; }
	table.data tbody td.actions { text-align: center; }
	table.data tfoot td { border-bottom: 1px solid #000; }
	table.data tbody tr:hover, table.data tbody tr.alt:hover, table.data tbody tr.alt.unrated:hover { background-color: #CCEBFF; }

	table.navigation td.alignLeft, table.navigation td.alignRight { min-width: 80px; }
	table,th,td
	{
	border:1px solid black;
	}


	</style>
		
	</head>
