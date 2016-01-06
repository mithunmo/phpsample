<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Scorpio Framework - {$oMap->getDescription()}</title>
		<link rel="home" title="Home" href="{$oMap->getUriPath()}" />
	</head>

	<body id="home">
		<h1>Scorpio Framework</h1>
		<div class="content">
			<div class="title">Static Pages</div>
			<div class="body">
				<p>Static pages are pages whose content rarely changes. If you are seeing this page, you either requested a page that does not exist, or did not request a page.</p>

				{assign var=pages value=$oModel->getStaticPages()}
				{if $pages->getArrayCount() > 0}
					<p>The following pages have been configured on this site:</p>
					<p>{foreach key=uriLink item=title from=$pages}
					<a href="/static/{$uriLink}" title="{$title}">{$title}</a><br />
					{/foreach}</p>
				{/if}
			</div>
		</div>
	</body>
</html>