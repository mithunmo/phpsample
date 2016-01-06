<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<style type="text/css">
		* { font-family: Verdana; font-size: 96%; }
		label { width: 10em; float: left; }
		label.error { float: none; color: red; padding-left: .5em; vertical-align: top; }
		p { clear: both; }
		.submit { margin-left: 12em; }
		em { font-weight: bold; padding-right: 1em; vertical-align: top; }
		div.folder { display: block; width: 90px; height: 80px; float: left; margin: 3px; padding: 3px; border: 1px solid #ccc; overflow: hidden }
		div.folder img { max-width: 80px; max-height: 80px; border: 0; }
		div.folder a div input{ display: block;}
		div.tinymenu { padding-bottom: 1em; padding-top:1em; }
	</style>
</head>

<body id="tinyBody">
	<div class="tinymenu">
		{if $oModel->getFolderPath() != "resources"}
			<a href="/tinymceAction/browse?folder={$parentPath}">BACK<img src="/themes/mofilm/images/icons/32x32/action-back.png"></a>
		{/if}
		<a href="/tinymceAction/showdir?folder={$oModel->getFolderPath()}">NEW FOLDER<img src="/themes/mofilm/images/icons/32x32/addfolder.png"></a>
		<a href="/tinymceAction/showupload?folder={$oModel->getFolderPath()}">UPLOAD<img src="/themes/mofilm/images/icons/32x32/upload.png"></a>
		<a href="#" id="tinyDelete">delete<img src="/themes/mofilm/images/icons/32x32/action-delete-object.png"></a>
	</div>

	<div class="results">
		{foreach $imageFile as $file}
			{if $file->isDir()}
				<div class="folder">
					<a href="/tinymceAction/browse?folder={$file->getOriginalFilename()}" id="{$oModel->getFullPath()}{$file->getOriginalFilename()}">
						<img src="/themes/mofilm/images/icons/64x64/folder.png" alt="icon" />						
					</a>
					<div class="filename"><input type="checkbox" id="{$oModel->getFullPath()}{$file->getOriginalFilename()}">{$file->getFilename()}</div>
				</div>
			{else}
				<div class="folder">
					<a href="#" onclick="selectImage('/{$file->getOriginalFilename()}'); return false;" title="Use image">
						<img src="/{$file->getOriginalFilename()}" alt="image" />
					</a>
				</div>
			{/if}
		{/foreach}
	</div>

	{foreach $oView->getResourcesByType('js') as $oResource}
		{$oResource->toString()}
	{/foreach}
</body>
</html>