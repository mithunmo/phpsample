{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=20}
{assign var=objects value=$oModel->getObjectList($offset, $limit)}
{assign var=totalObjects value=$oModel->getTotalObjects()}
{if $objects->getArrayCount() > 0}
<table class="data">
	<thead>
	<tr>
		<th class="first">&nbsp;</th>
		<th>{t}Name{/t}</th>
		<th>{t}Type{/t}</th>
		<th>{t}Language{/t}</th>
		<th>{t}HTML?{/t}</th>
		<th class="last">&nbsp;</th>
	</tr>
	</thead>
	<tfoot>
	{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=4}
	</tfoot>
	<tbody>
	{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=4}
		{foreach $objects as $oObject}
		<tr class="{cycle values="alt,"}">
			<td>{$oObject@iteration}</td>
			<td>{$oObject->getName()}</td>
			<td>{$oObject->getOutboundType()->getDescription()}</td>
			<td>{$oObject->getLanguage()}</td>
			<td>{if $oObject->getIsHtml()}Yes{else}No{/if}</td>
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
