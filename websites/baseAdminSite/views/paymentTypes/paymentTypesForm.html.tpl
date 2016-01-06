{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}ID{/t}</th>
				<td><input type="text" name="ID" value="{$oObject->getID()}" /></td>
			</tr>
			<tr>
				<th>{t}Name{/t}</th>
				<td><input type="text" name="Name" value="{$oObject->getName()}" /></td>
			</tr>
			<tr>
				<th>{t}Description{/t}</th>
				<td><input type="text" name="Description" value="{$oObject->getDescription()}" /></td>
			</tr>
			<tr>
				<th>{t}Moderation{/t}</th>
				<td><input type="text" name="Moderation" value="{$oObject->getModeration()}" /></td>
			</tr>
			<tr>
				<th>{t}Moderation Limit{/t}</th>
				<td><input type="text" name="ModerationLimit" value="{$oObject->getModerationLimit()}" /></td>
			</tr>
			<tr>
				<th>{t}Last Modified{/t}</th>
				<td><input type="text" name="LastModified" value="{$oObject->getLastModified()}" /></td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getDescription()}&quot;?{/t}</p>
{/if}