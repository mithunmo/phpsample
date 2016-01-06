<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<style>
		input { padding-bottom: 1em; }
	</style>
</head>

<body>
	<form method="POST" action="{$formaction}?folder={$oModel->getFolderPath()}" enctype="multipart/form-data" class="cmxform" id="tinymceForm">
		<input type="file" name="tinyMCEImagefile" id="tinyMCEImagefileID" />
		<br />
		<input type="submit" value="Upload" id="tinymceUpload" class="button"/>
	</form>

	{foreach $oView->getResourcesByType('js') as $oResource}
		{$oResource->toString()}
	{/foreach}
<body>
</html>