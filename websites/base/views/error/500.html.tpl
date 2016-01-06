<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Scorpio Framework - 500 - Internal Server Error</title>
		<style type="text/css">
		{literal}
			* { font-family: Verdana, sans-serif; font-size: 12px; }
			h1 { font-size: 18px; }
			h2 { font-size: 16px; }
			h3 { font-size: 14px; color: #f00; font-weight: bold; }
			table tr { vertical-align: top; }
			table thead th { border-bottom: 2px solid #000; text-align: left; }
			table tbody td { border-bottom: 1px solid #666; text-align: left; }
			table tbody td p { margin-top: 0; }
			table tbody code { font-family: monospace; }
			.fileSourceToggle { cursor: pointer; }
		{/literal}
		</style>
	</head>

	<body id="home">
		<h1>Internal Server Error</h1>
		<p>An unrecoverable internal error was encountered. This has been logged.</p>
		{include file=$oView->getTemplateFile('debug', '/error')}
	</body>
</html>