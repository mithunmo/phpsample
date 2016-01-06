{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=20}
{assign var=objects value=$oModel->getObjectList($offset, $limit)}
{assign var=totalObjects value=$oModel->getTotalObjects()}
{if $objects->getArrayCount() > 0}
	<table class="data">
		<thead>
			<tr>
				<th class="first"></th>
				<th>{t}Code{/t}</th>
				<th>{t}Description{/t}</th>
				<th>{t}Location{/t}</th>
				<th>{t}Start Date{/t}</th>
				<th>{t}End Date{/t}</th>
				<th class="last">&nbsp;</th>
			</tr>
		</thead>
		<tfoot>
			{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=6}
		</tfoot>
		<tbody>
		{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=6}
		{foreach $objects as $oObject}
			<tr class="{cycle values="alt,"}">
				<td>{$oObject@iteration+$offset}</td>
				<td>{$oObject->getCode()}</td>
				<td>{$oObject->getDescription()}</td>
				<td>{$oObject->getLocation()}</td>
				<td>{$oObject->getStartDate()}</td>
				<td>{$oObject->getEndDate()}</td>
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
