{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}List ID{/t}</th>
				<td><select>
					{foreach $oList as $oListObj}
						<option value="{$oListObj->getID()}" {if $oListObj->getID() == $oObject->getListID()} selected="selected"{/if}> {$oListObj->getName()} </option>
					{/foreach}
				</select></td>
			</tr>
			<tr>
				<th>{t}Subscribed{/t}</th>
				<td><input type="text" name="Subscribed" value="{$oObject->getSubscribed()}" /></td>
			</tr>
			<tr>
				<th>{t}Hash{/t}</th>
				<td><input type="text" name="Hash" value="{$oObject->getHash()}" /></td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record ID &quot;{$oController->getPrimaryKey()}&quot;?{/t}</p>
{/if}