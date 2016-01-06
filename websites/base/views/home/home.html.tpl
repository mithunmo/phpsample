<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Scorpio Framework - {$oMap->getDescription()}</title>
		<link rel="home" title="Home" href="{$oMap->getUriPath()}" />
	</head>

	<body id="home">
		<h1>Scorpio Framework</h1>
		<p>Well done! If you can see this text, then the base system is functioning.</p>
		<p>You should be able to make any request to {$oMap->getUriPath()} and end up with this page.</p>
		<p>Current request: <em>{$oRequest->getRequestUri()}</em></p>
	</body>
</html>