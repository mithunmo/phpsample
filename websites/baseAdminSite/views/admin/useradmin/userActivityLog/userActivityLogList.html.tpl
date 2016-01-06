{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=20}
{assign var=userID value=$oController->getSearchParameter('UserID', '')}
{assign var=logType value=$oController->getSearchParameter('Type', '')}
{assign var=description value=$oController->getSearchParameter('Description', '')}
{assign var=objects value=$oModel->getObjectList($offset, $limit, $userID, $logType, $description)}
{assign var=totalObjects value=$oModel->getTotalObjects()}

<div class="filters">
	<input type="text" name="UserID" value="{if $userID}{$userID}{else}{t}Search by UserID{/t}{/if}" class="small" />

	<select name="Type" size="1">
		<option value="">Any Log Type</option>
		<option value="Login" {if $logType == 'Login'}selected="selected"{/if}>Login</option>
		<option value="Upload" {if $logType == 'Upload'}selected="selected"{/if}>Upload</option>
		<option value="Other" {if $logType == 'Other'}selected="selected"{/if}>Other</option>
	</select>
	
	<input type="text" name="Description" value="{$description|default:'{t}Search by keyword{/t}'}" class="medium" />
</div>

{if $objects->getArrayCount() > 0}
	<table class="data">
		<thead>
			<tr>
				<th class="first">{t}ID{/t}</th>
				<th style="width: 80px;">{t}User ID{/t}</th>
				<th>{t}Timestamp{/t}</th>
				<th>{t}Type{/t}</th>
				<th class="last" style="width: 300px;">{t}Description{/t}</th>
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
				<td>{$oObject->getUserID()}</td>
				<td>{$oObject->getTimestamp()}</td>
				<td>{$oObject->getType()}</td>
				<td>{$oObject->getDescription()}</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
{else}
	<p>{t}No objects found in system.{/t}</p>
{/if}
