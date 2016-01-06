{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Type Name{/t}</th>
				<td><input type="text" name="TypeName" value="{$oObject->getTypeName()}" class="string" /></td>
			</tr>
			<tr>
				<th>{t}Description{/t}</th>
				<td><textarea name="Description" cols="60" rows="3" class="long">{$oObject->getDescription()}</textarea></td>
			</tr>
			<tr>
				<th>{t}Visible{/t}</th>
				<td>{booleanSelect name="Visible" selected=$oObject->getVisible() true="Yes" false="No"}</td>
			</tr>
			<tr>
				<th>{t}Class Name{/t}</th>
				<td><input type="text" name="ClassName" value="{$oObject->getClassName()}" class="string" /></td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getTypeName()}&quot;?{/t}</p>
{/if}