{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Type Name{/t}</th>
				<td><input type="text" name="TypeName" value="{$oObject->getTypeName()}" class="string" /></td>
			</tr>
			<tr>
				<th>{t}Send To Inbox{/t}</th>
				<td>{booleanSelect name="SendToInbox" selected=$oObject->getSendToInbox() true="Yes" false="No"}</td>
			</tr>
			<tr>
				<th>{t}Send To User Email{/t}</th>
				<td>{booleanSelect name="SendToUserEmail" selected=$oObject->getSendToUserEmail() true="Yes" false="No"}</td>
			</tr>
			<tr>
				<th>{t}Send To Group{/t}</th>
				<td>{booleanSelect name="SendToGroup" selected=$oObject->getSendToGroup() true="Yes" false="No"}</td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record ID &quot;{$oController->getPrimaryKey()}&quot;?{/t}</p>
{/if}