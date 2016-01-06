<tr>
	<th>{t}The movie status is{/t}</th>
	<td>
		<select name="params[report.movie.status]" size="1">
			<option value="">Any status</option>
			{foreach mofilmMovieManager::getAvailableMovieStatuses() as $status}
			<option value="{$status}">{$status|xmlstring}</option>
			{/foreach}
		</select>
	</td>
</tr>