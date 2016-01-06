{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Description{/t}</th>
				<td><input type="text" name="Description" value="{$oObject->getDescription()}" class="string" /></td>
			</tr>
			<tr>
				<th>{t}Gateway{/t}</th>
				<td>
					<select name="GatewayID" size="1">
						{foreach $gateways as $oGateway}
							<option value="{$oGateway->getGatewayID()}" {if $oObject->getGatewayID() == $oGateway->getGatewayID()}selected="selected"{/if}>{$oGateway->getDescription()}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<th>{t}Prs{/t}</th>
				<td><input type="text" name="Prs" value="{$oObject->getPrs()}" /></td>
			</tr>
			<tr>
				<th>{t}Active{/t}</th>
				<td>{booleanSelect name='Active' selected=$oObject->getActive() true='Yes' false='No'}</td>
			</tr>
			<tr>
				<th>{t}Network ID{/t}</th>
				<td>{networkSelect name='NetworkID' selected=$oObject->getNetworkID()}</td>
			</tr>
			<tr>
				<th>{t}Tariff{/t}</th>
				<td><input type="text" name="Tariff" value="{$oObject->getTariff()}" class="number" /></td>
			</tr>
			<tr>
				<th>{t}Country ID{/t}</th>
				<td>{territorySelect name='CountryID' selected=$oObject->getCountryID()}</td>
			</tr>
			<tr>
				<th>{t}Currency ID{/t}</th>
				<td>{currencySelect name='CurrencyID' selected=$oObject->getCurrencyID()}</td>
			</tr>
			<tr>
				<th>{t}Require Acknowledgement{/t}</th>
				<td>{booleanSelect name='RequireAcknowledgement' selected=$oObject->getRequireAcknowledgement() true='Yes' false='No'}</td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getDescription()}&quot;?{/t}</p>
{/if}