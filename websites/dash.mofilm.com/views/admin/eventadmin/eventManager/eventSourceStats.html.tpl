{if $sourceStats->getArrayCount() > 0}
	<table class="data">
		<thead>
			<tr>
				<th class="title">Brand</th>
				<th>App</th>
				<th>Rej</th>
				<th>Pen</th>
				<th>Tot</th>
			</tr>
		</thead>
		<tbody>
	{foreach $sourceStats as $oStats}
		{if $oStats->getOptionsSet()->getCount() > 0}
			<tr class="{cycle values='alt,'}">
				<th>{$oStats->getSource()->getName()|xmlstring}</th>
				<td>{$oStats->getTotalApproved()}</td>
				<td>{$oStats->getTotalRejected()}</td>
				<td>{$oStats->getTotalPending()}</td>
				<td>{$oStats->getTotalMovies()}</td>
			</tr>
		{/if}
	{/foreach}
		</tbody>
	</table>
{else}
	{t}No statistics found for the event.{/t}
{/if}