{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=20}
{assign var=objects value=$oModel->getObjectList($offset, $limit)}
{assign var=totalObjects value=$oModel->getTotalObjects()}
{if $objects->getArrayCount() > 0}
	<table class="data">
		<thead>
			<tr>
				<th class="first">{t}Account ID{/t}</th>
				<th>{t}Description{/t}</th>
				<th>{t}Prs{/t}</th>
				<th>{t}Tariff{/t}</th>
				<th>{t}Network{/t}</th>
				<th>{t}Active{/t}</th>
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
				<td>{$oObject->getGatewayAccountID()}</td>
				<td>{$oObject->getDescription()}</td>
				<td>{$oObject->getPrs()}</td>
				<td>{$oObject->getTariff()}</td>
				<td>{$oObject->getNetwork()->getDescription()}</td>
				<td>{if $oObject->getActive()}Yes{else}No{/if}</td>
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
