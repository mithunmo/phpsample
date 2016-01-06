{assign var=offset value=$oReports->getOffset()|default:0}
{assign var=limit value=$oReports->getLimit()}
{assign var=totalObjects value=$oReports->getTotalResults()}
<h2>{t}Scheduled Reports{/t}</h2>
<table class="data">
	<thead>
		<tr>
			<th>{t}Report Type{/t}</th>
			<th>{t}Schedule Type{/t}</th>
			<th>{t}Status{/t}</th>
			<th>{t}Created On{/t}</th>
			<th>{t}Last Scheduled{/t}</th>
			<th></th>
		</tr>
	</thead>
	<tfoot>
		{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=4}
	</tfoot>
	<tbody>
		{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=4}
		{if $oReports->getTotalResults() > 0}
			{foreach $oReports as $oReportSchedule}
			<tr>
				<td>
					{$oReportSchedule->getReportType()->getTypeName()}<br />
					<em>{$oReportSchedule->getReportTitle()}</em>
				</td>
				<td>{$oReportSchedule->getReportScheduleType()->getDescription()}</td>
				<td>{$oReportSchedule->getReportScheduleStatus()}</td>
				<td>{$oReportSchedule->getCreateDate()}</td>
				<td>{$oReportSchedule->getLastReportDate()}</td>
				<td>
					{strip}
					<a href="{$reportDeleteScheduleUri}/{$oReportSchedule->getReportScheduleID()}" title="{t}Delete schedule and all scheduled reports{/t}">
						<img src="{$themeicons}/32x32/action-delete-object.png" alt="{t}Delete{/t}" class="icon" />
					</a>
					{/strip}
				</td>
			</tr>
			{/foreach}
		{else}
			<tr>
				<td colspan="6">{t}You have no scheduled reports{/t}</td>
			</tr>
		{/if}
	</tbody>
</table>