<tr>
	<td colspan="2">{t}I want the report to{/t}</td>
</tr>
<tr>
	<th>{t}Be run{/t}</th>
	<td>{reportScheduleTypeSelect selected=1 name="ReportScheduleTypeID"}</td>
</tr>
<tr>
	<th>{t}On the following date{/t}</th>
	<td>
		<input type="text" name="ScheduledDate" class="datepicker date" value="{$smarty.now|date_format:'%Y-%m-%d'}" title="{t}Schedule date in year-month-day format e.g. Jan 1st 2010 is 2010-01-01{/t}" />
		@
		{html_select_time display_seconds=false minute_interval=15 prefix='' field_array='ScheduleTime'}
		<em>{t}GMT{/t}</em>
	</td>
</tr>