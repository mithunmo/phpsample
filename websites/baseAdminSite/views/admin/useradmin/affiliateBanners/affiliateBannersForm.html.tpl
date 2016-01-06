{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Description{/t}</th>
				<td><input type="text" name="Description" value="{$oObject->getDescription()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Link Url{/t}</th>
				<td><input type="text" name="Url" value="{$oObject->getUrl()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Affiliate{/t}</th>
				<td><input type="text" name="Affiliate" value="{$oObject->getAffiliate()}" class="long" /></td>
			</tr>
			{if $oObject->getID() > 0}
			<tr>
				<th>{t}Affiliate Link{/t}</th>
				<td>{$wwwMofilmUri}/link/{$oObject->getID()}</td>
			</tr>
			{/if}
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getDescription()}&quot;?{/t}</p>
{/if}