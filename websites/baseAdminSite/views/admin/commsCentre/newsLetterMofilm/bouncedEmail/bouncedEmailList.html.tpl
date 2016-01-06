{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=20}
{assign var=objects value=$oModel->getObjectList($offset, $limit)}
{assign var=totalObjects value=$oModel->getTotalObjects()}
{if $objects->getArrayCount() > 0}
	<table class="data">
		<thead>
			<tr>
				<th class="first">{t}ID{/t}</th>
				<th>{t}Email Address{/t}</th>
				<th>{t}Error Description{/t}</th>
				<th>{t}Sender Address{/t}</th>
			</tr>
		</thead>
		<tfoot>
			{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=2}
		</tfoot>
		<tbody>
		{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=2}
		{foreach $objects as $oObject}
			<tr class="{cycle values="alt,"}">
				<td>{$oObject->getID()}</td>
				<td>{$oObject->getEmailAddress()}</td>
				<td>{$oObject->getErrorDescription()}</td>
				<td>{$oObject->getSenderAddress()}</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
{else}
	<p>{t}No objects found in system.{/t}</p>
{/if}
