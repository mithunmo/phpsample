{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=20}
{assign var=eventID value=$oController->getSearchParameter('EventID')}
{assign var=objects value=$oModel->getObjectList($offset, $limit, $eventID, false)}
{assign var=totalObjects value=$oModel->getTotalObjects()}

<div class="filters">
	{eventSelect name='EventID' selected=$eventID class="valignMiddle long"}
</div>

{if $objects->getArrayCount() > 0 && $eventID > 0}
	<table class="data">
		<thead>
			<tr>
				<th class="first">ID</th>
				<th>{t}Brand{/t}</th>
				<th>{t}Project{/t}</th>
                                <th>{t}Sponsor{/t}</th>
				<th>{t}Public{/t}</th>
                                <th>{t}Status{/t}</th>
                                <th>{t}Preview Link{/t}</th>
				<th>{t}Upload link{/t} </th>
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
				<td>{$oObject->getID()}</td>
				<td>{$oObject->getName()}</td>
				<td>{$oObject->getEvent()->getName()}</td>
                                <td>{$oModel->getUser($oObject->getSponsorID())}</td>
				<td>{if $oObject->getHidden() == "Y"}No{else}Yes{/if}</td>
                                <td>{$oObject->getSourceStatus()}</td>
                                <td>{if $oObject->getHidden() == "Y"} - {else}<a target="_blank" href="{$oModel->getlink($oObject->getID())}"> Link </a>{/if}</td>
				<td><a target="_blank" href="https://mofilm.com/accounts/upload?eventID={$oObject->getEvent()->getID()}&sourceID={$oObject->getID()}">Link</a></td>
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
