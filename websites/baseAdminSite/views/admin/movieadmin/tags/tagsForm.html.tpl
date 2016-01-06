{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Name{/t}</th>
				<td><input type="text" name="Name" value="{$oObject->getName()}" class="string" /></td>
			</tr>
			<tr>
				<th>{t}Type{/t}</th>
				<td>
					<select name="Type" size="1">
						<option name="genre" {if $oObject->getType() == 'genre'}selected="selected"{/if}>Genre</option>
						<option name="tag" {if $oObject->getType() == 'tag'}selected="selected"{/if}>Tag</option>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getName()}&quot;?{/t}</p>
{/if}