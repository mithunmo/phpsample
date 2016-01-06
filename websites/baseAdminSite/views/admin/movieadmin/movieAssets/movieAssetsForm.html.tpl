{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Movie ID{/t}</th>
				<td><input type="text" name="MovieID" value="{$oObject->getMovieID()}" class="integer" /></td>
			</tr>
			<tr>
				<th>{t}Type{/t}</th>
				<td><input type="text" name="Type" value="{$oObject->getType()}" /></td>
			</tr>
			<tr>
				<th>{t}Ext{/t}</th>
				<td><input type="text" name="Ext" value="{$oObject->getExt()}" /></td>
			</tr>
			<tr>
				<th>{t}Profile ID{/t}</th>
				<td><input type="text" name="ProfileID" value="{$oObject->getProfileID()}" /></td>
			</tr>
			<tr>
				<th>{t}Description{/t}</th>
				<td><input type="text" name="Description" value="{$oObject->getDescription()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Width{/t}</th>
				<td><input type="text" name="Width" value="{$oObject->getWidth()}" class="integer" /></td>
			</tr>
			<tr>
				<th>{t}Height{/t}</th>
				<td><input type="text" name="Height" value="{$oObject->getHeight()}" class="integer" /></td>
			</tr>
			<tr>
				<th>{t}Filename{/t}</th>
				<td><input type="text" name="Filename" value="{$oObject->getFilename()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Cdn URL{/t}</th>
				<td><input type="text" name="CdnURL" value="{$oObject->getCdnURL()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Notes{/t}</th>
				<td><input type="text" name="Notes" value="{$oObject->getNotes()}" class="long" /></td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getDescription()}&quot;?{/t}</p>
{/if}