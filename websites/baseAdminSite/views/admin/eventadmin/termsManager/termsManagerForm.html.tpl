{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Replaces Terms{/t}</th>
				<td>{termsSelect name='ReplacesTerms' selected=$oObject->getReplacesTerms()}</td>
			</tr>
			<tr>
				<th>{t}Description{/t}</th>
				<td><input type="text" name="Description" value="{$oObject->getDescription()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Html Link{/t}</th>
				<td><input type="text" name="HtmlLink" value="{$oObject->getHtmlLink()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Pdf Link{/t}</th>
				<td><input type="text" name="PdfLink" value="{$oObject->getPdfLink()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Current Version{/t}</th>
				<td>{$oObject->getVersion()}</td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getDescription()}&quot;?{/t}</p>
{/if}