<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
</head>

<body>
	<div class="path">
		{if $oModel->getFolderPath() != "resources"}
			<a href="/tinymceAction/browse?folder={$parentPath}"><img src="/themes/mofilm/images/icons/32x32/action-back.png"></a>
		{/if}
	</div>
	{$message}
</body>
</html>
