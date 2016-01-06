{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="ID" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Domain Name{/t}</th>
				<td>{siteSelect name="DomainName" selected=$oObject->getDomainName()}</td>
			</tr>
			<tr>
				<th>{t}Reference{/t}</th>
				<td><input type="text" name="Reference" value="{$oObject->getReference()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Language{/t}</th>
				<td>{languageSelect name='Language' useISO=true selected=$oObject->getLanguage()}</td>
			</tr>
			<tr>
				<th>{t}Title{/t}</th>
				<td><input type="text" name="Title" value="{$oObject->getTitle()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Tags{/t}</th>
				<td>
					<input type="text" id="HelpPageTags" name="Tags" value="{$oObject->getTagSet()->getObjectsAsString()}" class="long" /><br />
					<em>{t}Separate tags with a comma. Tags can contain spaces too.{/t}</em>
				</td>
			</tr>
			<tr>
				<th>{t}Content{/t}</th>
				<td><textarea id="Content" name="Content" rows="10" cols="50" style="width: 80%; height: 600px;" class="tinymce">{$oObject->getContent()|escape:'htmlall':'UTF-8'}</textarea></td>
			</tr>
			<tr>
				<th>{t}Related Help Pages{/t}</th>
				<td>
					<em>{t}Ctrl+Click to select multiple related articles.{/t}</em><br />
					<select name="RelatedHelpTitles[]" multiple="multiple" size="5" class="long">
					{foreach $oModel->getObjectList() as $oRelatedHelpTitle}
						{if $oRelatedHelpTitle->getID() != $oObject->getID()}
							<option value="{$oRelatedHelpTitle->getID()}" {if $oModel->getRelatedSet()->getObjectById($oRelatedHelpTitle->getID())}selected="selected"{/if}>{$oRelatedHelpTitle->getDomainName()} - {$oRelatedHelpTitle->getTitle()}</option>
						{/if}
					{/foreach}
					</select>
				</td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getTitle()|xmlstring}&quot; on site &quot;{$oObject->getDomainName()|xmlstring}&quot;?{/t}</p>
{/if}