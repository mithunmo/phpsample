{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Artist ID{/t}</th>
				<td><input type="text" name="ArtistID" value="{$oObject->getArtistName()}" /></td>
			</tr>
			<tr>
				<th>{t}Track Name{/t}</th>
				<td><input type="text" name="TrackName" value="{$oObject->getSongName()}" /></td>
			</tr>
			<tr>
				<th>{t}Path{/t}</th>
				<td><input type="text" name="Path" value="{$oObject->getPath()}" /></td>
			</tr>
			<tr>
				<th>{t}Duration{/t}</th>
				<td><input type="text" name="Duration" value="{$oObject->getDuration()}" /></td>
			</tr>
			<tr>
				<th>{t}Source{/t}</th>
				<td><input type="text" name="Source" value="{$oObject->getMusicSource()}" /></td>
			</tr>
				<th>{t}Description{/t}</th>
				<td><input type="text" name="Description" value="{$oObject->getDescription()}" /></td>
			</tr>			
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record ID &quot;{$oController->getPrimaryKey()}&quot;?{/t}</p>
{/if}