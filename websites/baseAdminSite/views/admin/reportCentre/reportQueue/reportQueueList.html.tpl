{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=20}
{assign var=objects value=$oModel->getObjectList($offset, $limit)}
{assign var=totalObjects value=$oModel->getTotalObjects()}
{if $objects->getArrayCount() > 0}
	<table class="data">
		<thead>
			<tr>
				<th class="first">{t}Scheduled{/t}</th>
				<th>{t}Report Title{/t}</th>
				<th>{t}Owner{/t}
				<th class="last">&nbsp;</th>
			</tr>
		</thead>
		<tfoot>
			{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=2}
		</tfoot>
		<tbody>
		{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=2}
		{foreach $objects as $oObject}
			<tr class="{cycle values="alt,"}">
				<td>{$oObject->getScheduled()}</td>
				<td>{$oObject->getReport()->getReportSchedule()->getReportTitle()}</td>
				<td>{$oUser = mofilmUserManager::getInstanceById($oObject->getReport()->getUserID())}{$oUser->getFullname()}</td>
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
