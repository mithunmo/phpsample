{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}ID{/t}</th>
				<td><input type="text" name="ID" value="{$oObject->getID()}" /></td>
			</tr>
			<tr>
				<th>{t}Track ID{/t}</th>
				<td><input type="text" name="TrackID" value="{$oObject->getTrackID()}" /></td>
			</tr>
			<tr>
				<th>{t}License ID{/t}</th>
				<td><input type="text" name="LicenseID" value="{$oObject->getLicenseID()}" /></td>
			</tr>
			<tr>
				<th>{t}User ID{/t}</th>
				<td><input type="text" name="UserID" value="{$oObject->getUserID()}" /></td>
			</tr>
			<tr>
				<th>{t}Track Name{/t}</th>
				<td><input type="text" name="TrackName" value="{$oObject->getTrackName()}" /></td>
			</tr>
			<tr>
				<th>{t}Status{/t}</th>
				<td><input type="text" name="Status" value="{$oObject->getStatus()}" /></td>
			</tr>
			<tr>
				<th>{t}Music Source{/t}</th>
				<td><input type="text" name="MusicSource" value="{$oObject->getMusicSource()}" /></td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record ID &quot;{$oController->getPrimaryKey()}&quot;?{/t}</p>
{/if}