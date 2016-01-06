{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Title{/t}</th>
				<td><input type="text" name="Title" value="{$oObject->getTitle()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Content{/t}</th>
				<td><textarea name="Content" cols="60" rows="8" class="long ckeditor">{$oObject->getContent()|escape:'htmlall':'UTF-8'}</textarea></td>
			</tr>
			<tr>
				<th>{t}Active{/t}</th>
				<td>
					{booleanSelect name=Active selected=$oObject->getActive() true="Yes" false="No"}
					<em>{t}Setting to active will make all other posts in-active.{/t}</em>
				</td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getTitle()}&quot;?{/t}</p>
{/if}