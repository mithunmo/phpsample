<tr>
	<td colspan="2">{t}And where{/t}</td>
</tr>
<tr>
	<th>{t}With links for{/t}</th>
	<td>
		<input type="hidden" name="params[report.user.id]" value="{$oUser->getID()}" />
		<em>{$oUser->getFullname()|xmlstring}</em>
	</td>
</tr>
<tr>
	<th>{t}The rating is greater than{/t}</th>
	<td>
		<select name="params[report.movie.rating]" size="1">
		{for $i=1; $i <= 10; $i++}
			<option value="{$i}" {if $i == 9}selected="selected"{/if}>{$i}</option>
		{/for}
		</select>
	</td>
</tr>
{include file=$oView->getTemplateFile('reportComponentEvent')}
