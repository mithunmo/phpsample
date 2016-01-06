{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Company Name{/t}</th>
				<td><input type="text" name="CompanyName" value="{$oObject->getCompanyName()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Email Contact{/t}</th>
				<td><input type="text" name="EmailContact" value="{$oObject->getEmailContact()}" class="long" /></td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record ID &quot;{$oController->getPrimaryKey()}&quot;?{/t}</p>
{/if}