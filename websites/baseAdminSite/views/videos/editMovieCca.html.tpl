<h3><a href="#">{t}MOFILM Filmmaker Pack{/t}</a></h3>
<div>
	{assign var=assetSet value=$oMovie->getAssetSet()->getObjectByAssetType(mofilmMovieAsset::TYPE_CCA)->getFirst()}
	{if $assetSet->getID() > 0 }
		CCA FILE UPLOADED : <a href="/download/ccaDownloads/{$oMovie->getID()}">Click Here to Download File</a>
		<br /><input id="CcaVerified" type="checkbox" name="CcaVerified" value="1" {if $assetSet->getNotes() == 'CCA VERIFIED'}checked{/if} /> Document Verified
		<br />
		<fieldset>
			<legend>Upload Access for Admin</legend>
			<input type="file" name="ccaFile" id="ccaFile" class="string" onclick="r=confirm('File Already uploaded.Do you want to reupload?'); if (r==false) { return false; } else { return true; }"/>
		</fieldset>
	{else}
		CCA File NOT UPLOADED.
		<br /><br />
		<fieldset>
			<legend>Upload Access for Admin</legend>
			<input type="file" name="ccaFile" id="ccaFile" class="string"/>
		</fieldset>
	{/if}
</div>