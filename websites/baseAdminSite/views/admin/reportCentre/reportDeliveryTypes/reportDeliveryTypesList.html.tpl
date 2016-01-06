{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=20}
{assign var=objects value=$oModel->getObjectList($offset, $limit)}
{assign var=totalObjects value=$oModel->getTotalObjects()}
{if $objects->getArrayCount() > 0}
	<table class="data">
		<thead>
			<tr>
				<th class="first"></th>
				<th>{t}Type Name{/t}</th>
				<th>{t}Send To Inbox{/t}</th>
				<th>{t}Send To Email{/t}</th>
				<th>{t}Send To Group{/t}</th>
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
				<td>{$oObject@iteration+$offset}</td>
				<td>{$oObject->getTypeName()}</td>
				<td>{if $oObject->getSendToInbox()}Yes{else}No{/if}</td>
				<td>{if $oObject->getSendToUserEmail()}Yes{else}No{/if}</td>
				<td>{if $oObject->getSendToGroup()}Yes{else}No{/if}</td>
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
