<tr>
	<th>{t}Deliver to{/t}</th>
	<td>{reportDeliveryTypeSelect selected=1 name="DeliveryTypeID" id="reportDeliveryType"}</td>
</tr>
<tr id="reportDeliveryOutputType">
	<th>{t}I would like the report as a{/t}</th>
	<td>
		<select name="params[report.outputType]" size="1">
			{foreach $oModel->getReportType()->getOutputTypes() as $output}
			<option value="{$output}" {if $output == 'xlsx'}selected="selected"{/if}>{$output}</option>
			{/foreach}
		</select>
		file
	</td>
</tr>