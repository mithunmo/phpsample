<tr>
	<th>{t}Using the dates for{/t}</th>
	<td>
		<select name="params[report.dateRange]" size="1">
			<option value="">{t}What I Choose{/t}</option>
			<optgroup label="{t}Relative dates in the past{/t}">
				<option value="yesterday">{t}Yesterday{/t}</option>
				<option value="lastweek">{t}Last Week{/t}</option>
				<option value="lastfortnight">{t}Last Fortnight{/t}</option>
				<option value="lastmonth" selected="selected">{t}Last Month{/t}</option>
				<option value="lastquarter">{t}Last Quarter{/t}</option>
				<option value="last6months">{t}Last 6 Months{/t}</option>
				<option value="lastyear">{t}Last Year{/t}</option>
			</optgroup>
			<optgroup label="{t}Relative dates in the now and future{/t}">
				<option value="today">{t}Today{/t}</option>
				<option value="thisweek">{t}This Week{/t}</option>
				<option value="thisfortnight">{t}This Fortnight{/t}</option>
				<option value="thismonth">{t}This Month{/t}</option>
				<option value="thisquarter">{t}This Quarter{/t}</option>
				<option value="thisyear">{t}This Year{/t}</option>
			</optgroup>
		</select>
	</td>
</tr>
<tr>
	<td colspan="2">{t}or between the following dates{/t}</td>
</tr>
<tr>
	<th>{t}Start on{/t}</th>
	<td>
		<input type="text" name="params[report.startDate]" class="datepicker date" title="{t}Start date in year-month-day format e.g. Jan 1st 2010 is 2010-01-01{/t}" />
		<em>{t}GMT{/t}</em>
	</td>
</tr>
<tr>
	<th>{t}End on{/t}</th>
	<td>
		<input type="text" name="params[report.endDate]" class="datepicker date" title="{t}End date in year-month-day format e.g. Jan 1st 2010 is 2010-01-01{/t}" />
		<em>{t}GMT{/t}</em>
	</td>
</tr>