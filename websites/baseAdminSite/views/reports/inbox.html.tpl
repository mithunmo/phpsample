{assign var=offset value=$oReports->getOffset()|default:0}
{assign var=limit value=$oReports->getLimit()}
{assign var=totalObjects value=$oReports->getTotalResults()}
<h2>{t}Report Inbox{/t}</h2>
<p>{t}This page will refresh automatically every 20 seconds.{/t}</p>
<table class="data" id="reportInbox">
	<thead>
		<tr>
			<th>{t}Report Title{/t}</th>
			<th>{t}Run Date{/t}</th>
			<th style="width: 50px;">{t}Status{/t}</th>
			<th style="width: 60px;">{t}Download{/t}</th>
			<th style="width: 80px;"></th>
		</tr>
	</thead>
	<tfoot>
		{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=3}
	</tfoot>
	<tbody>
		{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=3}
		{if $oReports->getTotalResults() > 0}
			{foreach $oReports as $oReport}
			{assign var=oRepParams value=$oReport->getReportSchedule()->getParamSet()}
			<tr>
				<td>
					{$oReport->getReportSchedule()->getReportTitle()}<br />
					<span style="font-size: 9px;">
						{$oReport->getReportSchedule()->getReportType()->getTypeName()}
						{if $oRepParams->getParam('report.startDate')} from: {$oRepParams->getParam('report.startDate')}{/if}
						{if $oRepParams->getParam('report.endDate')} until: {$oRepParams->getParam('report.endDate')}{/if}
						{if $oRepParams->getParam('report.event.id')} for event: {$oReport->getReportSchedule()->getReportInstance()->getEvent()->getName()}{/if}
						{if $oRepParams->getParam('report.source.id')} for source: {$oReport->getReportSchedule()->getReportInstance()->getSource()->getName()}{/if} 
					</span>
				</td>
				<td>{$oReport->getRequestDate()|date_format:'%d/%m @ %H:%M'}</td>
				<td class="alignCenter">
					{if $oReport->getReportStatus()->getReportStatusID() == 6}
						<img src="{$themeimages}/messagebox/success.png" alt="{t}Completed Successfully{/t}" title="{t}Completed Successfully{/t}" />
					{elseif $oReport->getReportStatus()->getReportStatusID() == 5}
						<img src="{$themeimages}/messagebox/warning.png" alt="{t}Completed With No Results{/t}" title="{t}Completed With No Results{/t}" />
					{elseif $oReport->getReportStatus()->getReportStatusID() == 7}
						<img src="{$themeimages}/messagebox/critical.png" alt="{t}Failed to Complete{/t}" title="{t}Failed to Complete{/t}" />
					{else}
						<img src="{$themeimages}/messagebox/information.png" alt="{$oReport->getReportStatus()->getDescription()}" title="{$oReport->getReportStatus()->getDescription()}" />
					{/if}
				</td>
				<td class="alignCenter">
					{strip}
					{if $oReport->getReportStatusID() == reportCentreReportStatus::S_COMPLETED}
						{assign var=output value=$oRepParams->getParam('report.outputType')}
						<a href="{$reportDownloadUri}/{$oReport->getReportID()}/format/{$output}" title="{t}Download report in {$output} format{/t}">
							<img src="{$themeicons}/32x32/{$oReport->getReportSchedule()->getReportType()->getIcon($output)}" alt="{$output}" class="icon" />
						</a>
					{/if}
					{/strip}
				</td>
				<td>
					{strip}
					<a href="{$reportDeleteUri}/{$oReport->getReportID()}" title="{t}Delete report{/t}">
						<img src="{$themeicons}/32x32/action-delete-object.png" alt="{t}Delete{/t}" class="icon" />
					</a>
					{/strip}
					{strip}
					<a href="{$reportRefreshUri}/{$oReport->getReportID()}" title="{t}Refresh report{/t}">
						<img src="{$themeicons}/32x32/action-view-objects.png" alt="{t}Refresh{/t}" class="icon" />
					</a>
					{/strip}
				</td>
			</tr>
			{/foreach}
		{else}
			<tr>
				<td colspan="5">{t}There are no reports in your inbox{/t}</td>
			</tr>
		{/if}
	</tbody>
</table>