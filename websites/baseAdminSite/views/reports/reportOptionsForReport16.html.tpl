<tr>
	<td colspan="2">{t}And where{/t}</td>
</tr>
<tr>
	<th>{t}The winners are placed up to{/t}</th>
	<td>
		<select name="params[report.award.position]" size="1">
		{for $i=1; $i <= 5; $i++}
			<option value="{$i}" {if $i == 1}selected="selected"{/if}>{$i}{if $i == 1}st{elseif $i == 2}nd{elseif $i == 3}rd{else}th{/if}</option>
		{/for}
		</select>
	</td>
</tr>