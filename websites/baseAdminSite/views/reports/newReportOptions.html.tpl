{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Reports - Create Report{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<div class="floatLeft sideBar">
				{include file=$oView->getTemplateFile('reportMenu')}
			</div>

			<div class="floatLeft main">
				<h2>{t}Create Report{/t} - {$oModel->getReportType()->getTypeName()}</h2>
				<p>
					{t}You can now customise how this report will be run, when it will be run and the time period it should cover.{/t}
				</p>
				<p>
					{t}Please note:{/t}
				</p>
				<ul>
					<li>{t}Not all reports support all options{/t}</li>
					<li>{t}If you schedule a report to recur, it will next run that period of time from when you first schedule the report{/t}</li>
					<li>{t}For scheduled reports you should use a relative date range, otherwise the chosen dates will always be used{/t}</li>
					<li>{t}Reports are run on a queue and you may have to wait before your report is processed{/t}</li>
					<li>{t}Event based reports will only run between the event start and end dates{/t}</li>
					<li>{t}Only the selected output type will be available when you run the report (default Excel 2007+ XLSX){/t}</li>
					<li>{t}All reports use the GMT timezone{/t}</li>
				</ul>
				
				<form action="{$reportSaveUri}" enctype="multipart/form-data" method="post" id="reportForm">
					<div class="hidden">
						<input type="hidden" name="ReportTypeID" value="{$oModel->getReportTypeID()}" />
						<input type="hidden" name="params[report.email.address]" value="{$oUser->getEmail()|xmlstring}" />
					</div>
					<table class="data">
						<tfoot>
							<tr>
								<td colspan="2" class="alignRight">
									<button type="reset" name="reset" value="{t}Reset{/t}">
										<img src="{$themeicons}/32x32/action-undo.png" alt="{t}Reset{/t}" class="icon" />
										{t}Reset{/t}
									</button>
									
									<button type="submit" name="save" value="{t}Save{/t}" id="reportSubmit">
										<img src="{$themeicons}/32x32/action-do-new-object.png" alt="{t}Save{/t}" class="icon" />
										{t}Save{/t}
									</button>
								</td>
							</tr>
						</tfoot>
						<tbody>
							<tr>
								<th>{t}Title this report{/t}</th>
								<td><input type="text" name="ReportTitle" maxlength="255" class="long" /></td>
							</tr>
							{include file=$oView->getTemplateFile('reportComponentDeliveryMethod')}
							{include file=$oView->getTemplateFile('reportComponentScheduleType')}
							
							{include file=$oView->getTemplateFile($reportOptions)}
						</tbody>
					</table>
				</form>
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}