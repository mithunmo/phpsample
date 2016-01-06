{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Name{/t}</th>
				<td><input type="text" name="Name" value="{$oObject->getName()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Html template{/t}</th>
				<td>
				<textarea id="elm1" name="HtmlTemplate" rows="15" cols="75" style="width: 75%;height: 500px" class="tinymce">{$oObject->getHtmlTemplate()}</textarea>
				</td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getName()}&quot;?{/t}</p>
{/if}