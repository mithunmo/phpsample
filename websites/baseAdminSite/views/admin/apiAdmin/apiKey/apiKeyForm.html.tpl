{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Public Key{/t}</th>
				<td><input type="text" name="PublicKey" value="{$oObject->getPublicKey()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Private Key{/t}</th>
				<td><input type="text" name="PrivateKey" value="{$oObject->getPrivateKey()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Active{/t}</th>
				<td>
					{booleanSelect name="Active" selected=$oObject->getActive() true="Yes" false="No"}
				</td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record ID &quot;{$oController->getPrimaryKey()}&quot;?{/t}</p>
{/if}