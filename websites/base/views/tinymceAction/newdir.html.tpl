<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<style>
		input { padding-bottom: 1em; }
	</style>
</head>

<body>
	<form method="post" action="/tinymceAction/createdir" class="cmxform" id="tinymceNewfolder">
		<table>
				<tr>
					<th> Enter your directory name </th>
					<td><input type="text" name="newdir" style="height:10px ; width:200px" id="tinymceFolder"></td>
				</tr>
				<tr>
					<th> </th>
					<td><input type="hidden" name="folder" value="{$oModel->getFolderPath()}" >
					<input type="submit" class="button" value="Create Folder" id="tinymceCreatedir"></td>
				</tr>
		</table>
	</form>

	{foreach $oView->getResourcesByType('js') as $oResource}
		{$oResource->toString()}
	{/foreach}

<body>
</html>