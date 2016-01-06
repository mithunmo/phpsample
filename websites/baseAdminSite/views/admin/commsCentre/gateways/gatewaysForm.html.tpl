{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden">
		<input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" />
	</div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Description{/t}</th>
				<td><input type="text" name="Description" value="{$oObject->getDescription()}" class="string" /></td>
			</tr>
			<tr>
				<th>{t}Class Name{/t}</th>
				<td><input type="text" name="ClassName" value="{$oObject->getClassName()}" class="string" /></td>
			</tr>
			<tr>
				<th>{t}Transport Class{/t}</th>
				<td><input type="text" name="TransportClass" value="{$oObject->getTransportClass()}" class="string" /></td>
			</tr>
			<tr>
				<th>{t}Active{/t}</th>
				<td>{booleanSelect name='Active' selected=$oObject->getActive() true='Yes' false='No'}</td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getDescription()}&quot;?{/t}</p>
{/if}