{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=20}
{assign var=supplierID value=$oController->getSearchParameter('SupplierID')}
{assign var=sourceID value=$oController->getSearchParameter('SourceID')}
{assign var=objects value=$oModel->getObjectList($offset, $limit, $supplierID, $sourceID)}
{assign var=totalObjects value=$oModel->getTotalObjects()}

<div class="filters">
	{supplierSelect name='SupplierID' selected=$supplierID class="valignMiddle string"}
	{sourceSelect name='SourceID' selected=$sourceID class="valignMiddle string"}
</div>

{if $objects->getArrayCount() > 0}
	<table class="data">
		<thead>
			<tr>
				<th class="first"></th>
				<th>{t}Artist{/t}</th>
				<th>{t}Title{/t}</th>
				<th style="width: 120px;">{t}Supplier{/t}</th>
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
				<td>{if $oObject->getArtist()}{$oObject->getArtist()}{else}{$oObject->getDescription()}{/if}</td>
				<td>{$oObject->getTitle()}</td>
				<td>{$oObject->getSupplier()->getDescription()}</td>
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
