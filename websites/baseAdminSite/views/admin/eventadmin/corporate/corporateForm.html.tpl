{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden">
            <input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" />
            <input type="hidden" name="ID" value="{$oObject->getID()|default:0}" />
        </div>
	<table class="data">
		<tbody>

			<tr>
				<th>{t}Name{/t}</th>
				<td><input type="text" name="Name" value="{$oObject->getName()}" /></td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getName()}&quot;?{/t}</p>
{/if}