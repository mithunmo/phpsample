{assign var=domainName value=$oController->getSearchParameter('DomainName', null)}
{assign var=searchForValue value=$oController->getSearchParameter('searchValue', null)}
{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=20}
{assign var=objects value=$oModel->getObjectList($offset, $limit, $domainName, $searchForValue)}
{assign var=totalObjects value=$oModel->getTotalObjects()}

<div class="filters">
	{siteSelect name="DomainName" size="1" selected=$domainName}
	<input type="text" name="searchValue" value="{$searchForValue|default:'Value to search for'}" class="medium" onfocus="this.select()" />
</div>

{if $objects->getTotalResults() > 0}
	<table class="data">
		<thead>
			<tr>
				<th class="first">&nbsp;</th>
				<th>{t}Site and Reference{/t}</th>
				<th>{t}Title{/t}</th>
				<th>{t}Language{/t}</th>
				<th class="last">&nbsp;</th>
			</tr>
		</thead>
		<tfoot>
			{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=3}
		</tfoot>
		<tbody>
		{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=3}
		{foreach $objects as $oObject}
			<tr class="{cycle values="alt,"}">
				<td>{$oObject@iteration+$offset}</td>
				<td>({$oObject->getDomainName()}){$oObject->getReference()}</td>
				<td>{$oObject->getTitle()}</td>
				<td>{$oObject->getLanguage()}</td>
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
