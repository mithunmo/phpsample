{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Name{/t}</th>
				<td><input type="text" name="Name" value="{$oObject->getName()}" /></td>
			</tr>
			<tr>
				<th>{t}Type{/t}</th>
				<td> 
					<select name="Type">
					{foreach from=$oType  key=oKey item=oVal}
						<option value="{$oVal}"> {$oKey} </option>
					{/foreach}
					</select>
					
				</td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getName()}&quot;?{/t}</p>
{/if}