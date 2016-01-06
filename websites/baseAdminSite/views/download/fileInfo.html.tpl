<script type="text/javascript">
	function fetchFile() {
		setTimeout("downloadFile()", 5000);
	}
	function downloadFile() {
		document.location.href = "{$oObject->getFileUri()}";
	}
	window.onload = fetchFile;
</script>

<div class="fileDetails">
	<div class="row">
		<div class="name">{t}Filename:{/t}</div>
		<div class="value">{$oObject->getShortFilename()|escape:'htmlall':'utf-8'}</div>
	</div>

	<div class="row">
		<div class="name">{t}Description:{/t}</div>
		<div class="value">{$oObject->getDescription()|escape:'htmlall':'utf-8'}</div>
	</div>

	<div class="row">
		<div class="name">{t}Filesize:{/t}</div>
		<div class="value">{$oObject->getFilesize()|escape:'htmlall':'utf-8'}</div>
	</div>
	
	<div class="altDownloadLink">
		{t}If your download does not start automatically, then download your file by <a href="{$oObject->getFileUri()}">clicking here</a>.{/t}
	</div>
</div>