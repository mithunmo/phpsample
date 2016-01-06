{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=30}
{assign var=objects value=$oModel->getObjectList($offset, $limit)}
{assign var=totalObjects value=$oModel->getTotalObjects()}
{if $objects->getArrayCount() > 0}
	<table class="data">
		<thead>
			<tr>
				<th class="first"></th>
				<th>{t}Scheduled{/t}</th>
				<th>{t}Recipient{/t}</th>
				<th>{t}Type{/t}</th>
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
				<td>{$oObject@iteration}
				<td>{$oObject->getScheduled()}</td>
				<td>
					{$oObject->getMessage()->getRecipient()}
					{if $oObject->getMessage()->getMessageSubject()}
						<br />
						<span class="subject">{$oObject->getMessage()->getMessageSubject()}</span>
					{/if}
				</td>
				<td>{$oObject->getMessage()->getOutboundType()->getDescription()}</td>
				<td class="actions">
					{include file=$oView->getTemplateFile('daoObjectListDefaultActions', '/shared') hideEdit=true}
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
{else}
	<p>{t}No objects found in system.{/t}</p>
{/if}
