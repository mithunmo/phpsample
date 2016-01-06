{assign var=emailAddress value=$oController->getSearchParameter('Email', '')}
{assign var=subListIDVal value=$oController->getSearchParameter('subListID', '0')}
{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=20}
{assign var=objects value=$oModel->getObjectList($subListIDVal,$offset, $limit)}
{assign var=totalObjects value=$oModel->getTotalObjects($subListIDVal)}
<select name ="subListID" onChange="this.form.submit()">
{foreach $oList as $oListObj}
	{if $oListObj->getID() == $subListIDVal}
		<option value="{$oListObj->getID()}" selected> {$oListObj->getName()} </option>
	{else}
		<option value="{$oListObj->getID()}"> {$oListObj->getName()} </option>
	{/if}
    {/foreach}
</select>
{if $objects->getArrayCount() > 0}
	<table class="data">
		<thead>
			<tr>
				<th class="first">{t}Email ID{/t}</th>
				<th>{t}List ID{/t}</th>
				<th>{t}Subscribed{/t}</th>
				<th>{t}Hash{/t}</th>
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
				<td>{$oModel->getEmailByEmailID($oObject->getEmailID())}</td>
				<td>{$oModel->getListNameByID($oObject->getListID())}</td>
				<td>{$oObject->getSubscribed()}</td>
				<td>{$oObject->getHash()}</td>
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
