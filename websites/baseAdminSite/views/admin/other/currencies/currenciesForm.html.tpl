{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Description{/t}</th>
				<td><input type="text" name="Description" value="{$oObject->getDescription()}" class="string" /></td>
			</tr>
			<tr>
				<th>{t}Iso Code String{/t}</th>
				<td><input type="text" name="IsoCodeString" value="{$oObject->getIsoCodeString()}" class="number" /></td>
			</tr>
			<tr>
				<th>{t}Iso Code Numeric{/t}</th>
				<td><input type="text" name="IsoCodeNumeric" value="{$oObject->getIsoCodeNumeric()}" class="number" /></td>
			</tr>
			<tr>
				<th>{t}Symbol{/t}</th>
				<td><input type="text" name="Symbol" value="{$oObject->getSymbol()}" class="number" /></td>
			</tr>
			<tr>
				<th>{t}Position{/t}</th>
				<td>
					<select name="Position" size="1">
						<option value="Before" {if $oObject->getPosition() == 'Before'}selected="selected"{/if}>Before</option>
						<option value="After" {if $oObject->getPosition() == 'After'}selected="selected"{/if}>After</option>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getDescription()}&quot;?{/t}</p>
{/if}