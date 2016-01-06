{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=20}
{assign var=sourceID value=$oController->getSearchParameter('SourceID')}
{assign var=fileType value=$oController->getSearchParameter('FileType')}
{assign var=objects value=$oModel->getObjectList($offset, $limit, $sourceID, $fileType)}
{assign var=totalObjects value=$oModel->getTotalObjects()}

<div class="filters">
	<select name="FileType" size="1" class="valignMiddle">
		<option value="">Not selected</option>
		{foreach $fileTypes as $fType}
		<option value="{$fType}" {if $fType == $fileType}selected="selected"{/if}>{$fType|capitalize}</option>
		{/foreach}
	</select>
	{sourceSelect name='SourceID' selected=$sourceID class='valignMiddle string'}
</div>

{if $objects->getArrayCount() > 0}
	<table class="data">
		<thead>
			<tr>
				<th class="first">ID</th>
				<th>{t}Description{/t}</th>
				<th>{t}Filetype{/t}</th>
				<th>{t}Filename{/t}</th>
				<th>{t}Lang{/t}</th>
				<th>{t}Uri{/t}</th>
				<th class="last">&nbsp;</th>
			</tr>
		</thead>
		<tfoot>
			{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=5}
		</tfoot>
		<tbody>
		{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=5}
		{foreach $objects as $oObject}
			<tr class="{cycle values="alt,"}">
				<td>{$oObject->getID()}</td>
				<td>{$oObject->getDescription()}</td>
				<td>{$oObject->getFiletype()}</td>
				<td>{$oObject->getFilename()}</td>
				<td>{$oObject->getLang()}</td>
				<td>{foreach $oObject->getSourceSet() as $oSource}
					{if $oObject->getFiletype() != "brief" }
					<a target="_blank" href="{if $oSource->isOpen()}https://mofilm.com/asset/{$oSource->getDownloadHash()}{else}http://admin.mofilm.com/download/generalDownloads?url={$oObject->getFileLocation()}{/if}">Link</a>
					{else}
					<a target="_blank" href="{if $oSource->isOpen()}https://mofilm.com/brief/{$oSource->getDownloadHash()}{else}http://admin.mofilm.com/download/generalDownloads?url={$oObject->getFileLocation()}{/if}">Link</a>	
					{/if}	
				    {/foreach}
				</td>
				<td class="actions">
					{include file=$oView->getTemplateFile('daoObjectListDefaultActions', '/shared')}
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
{else}
	<p>{t}No objects found in system.{/t}</p>
{/if}
