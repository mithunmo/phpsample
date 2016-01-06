{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Message Type{/t}</th>
				<td>
					<select name="MessageType" size="1">
						<option value="SMS" {if $oObject->getMessageType() == 'SMS'}selected="selected"{/if}>SMS</option>
						<option value="Email" {if $oObject->getMessageType() == 'Email'}selected="selected"{/if}>Email</option>
					</select>
				</td>
			</tr>
			<tr>
				<th>{t}Description{/t}</th>
				<td><input type="text" name="Description" value="{$oObject->getDescription()}" class="string" /></td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getDescription()}&quot;?{/t}</p>
{/if}