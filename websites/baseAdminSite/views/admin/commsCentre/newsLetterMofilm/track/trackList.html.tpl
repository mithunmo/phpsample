{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=20}
{assign var=nlID value=$oController->getSearchParameter('newslettertri')|default:1}
{assign var=objects value=$oModel->getObjectListByNlId($nlID,$offset, $limit)}
{*assign var=objects value=$oModel->getObjectList($offset, $limit)*}
{assign var=totalObjects value=$oModel->getTotalObjectsOfNl($nlID)}


<h3>{t}Select the newsletter{/t}</h3>
<select name="newslettertri" id="newslettertrack" onChange="this.form.submit()">
	<option>{t}select the newsletter{/t}</option>
	{foreach $newslettersent as $oNl}
		{if $oNl->getNlid() == $nlID}
			<option value="{$oNl->getNlid()}" selected> {$oNl->getName()} </option>
		{else}
			<option value="{$oNl->getNlid()}"> {$oNl->getName()} </option>
		{/if}
	{/foreach}
</select>


{if $objects->getArrayCount() > 0}
	<h4>{t}Percentage of People who have viewed the newsletter{/t}</h4>
	<input type="hidden" id="trackStats" value ="click to view percentage">
	<span class="progressBar" id="pb1">{$percentage}</span>
	<br>
	<h4>{t}Charts showing the number of people who viewwed the email on a date{/t}</h4>
	<br>
	<div id="chartdiv" style="height:400px;width:100%; "></div>
	<br>
	<h4>{t}List of people who have been sent a email{/t}</h4>
	<table class="data">
		<thead>

		<tr>
			<th class="first">{t}ID{/t}</th>
			<th>{t}Newsletter Name{/t}</th>
			<th>{t}Email {/t}</th>
			<th class="last">{t}Status{/t}</th>
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
				{assign var=nlName value=$oModel->getNlNameById($oObject->getNewsletterID())}
				<td>{$nlName}</td>
				{assign var=nlUser value=$oModel->getEmailById($oObject->getUserID())}
				<td>{$nlUser}</td>
				{if $oObject->getStatus() == 0}
					<td>{t}Not Viewed{/t}</td>
				{else}
					<td>{t}Viewed{/t}</td>
				{/if}
			</tr>
			{/foreach}

		</tbody>
	</table>
{else}
	<p>{t}This newsletter was not sent{/t}</p>
{/if}
