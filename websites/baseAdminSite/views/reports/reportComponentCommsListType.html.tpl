<tr>
	<th>{t}The list type is{/t}</th>
	<td>
		<select name="params[report.list.id]" size="1">
			<option value="">Select list type</option>
			{foreach mofilmCommsListType::listOfObjects() as $oListType}
			<option value="{$oListType->getID()}">{$oListType->getDescription()|xmlstring}</option>
			{/foreach}
		</select>
	</td>
</tr>