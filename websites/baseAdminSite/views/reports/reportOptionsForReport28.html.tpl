<tr>
	<td colspan="2">{t}And where{/t}</td>
</tr>
<tr>
	<th>{t}From date{/t}</th>
	<td>
		<input type="text" name="params[report.from]" class="datepicker date" value="{$smarty.now|date_format:'%Y-%m-%d'}" title="{t}Schedule date in year-month-day format e.g. Jan 1st 2010 is 2010-01-01{/t}" />
	</td>

</tr>
<tr>
	<th>{t}To date{/t}</th>
	<td>
		<input type="text" name="params[report.to]" class="datepicker date" value="{$smarty.now|date_format:'%Y-%m-%d'}" title="{t}Schedule date in year-month-day format e.g. Jan 1st 2010 is 2010-01-01{/t}" />
	</td>

</tr>
