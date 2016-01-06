{if $sourceStats->getArrayCount() > 0}
	<table class="data smallText">
		<thead>
			<tr>
				<th style="width: 200px;">Brand</th>
                                <th>Account Manager</th>
				<th>Approved</th>
				<th>Rejected</th>
				<th>Pending</th>
				<th>Total Movies</th>
			</tr>
		</thead>
		<tbody>
	{foreach $sourceStats as $oStats}
		{if $oStats->getOptionsSet()->getCount() > 0}
			<tr class="{cycle values='alt,'}">
				<th>{$oStats->getSource()->getName()|xmlstring}</th>
                                <td class="alignCenter">{$oStats->getSource()->getUser($oStats->getSource()->getSponsorID())}</td>
				<td class="alignCenter">{$oStats->getTotalApproved()}</td>
				<td class="alignCenter">{$oStats->getTotalRejected()}</td>
				<td class="alignCenter">{$oStats->getTotalPending()}</td>
				<td class="alignCenter">{$oStats->getTotalMovies()}</td>
			</tr>
		{/if}
	{/foreach}
		</tbody>
	</table>
{else}
	{t}No statistics found for the event.{/t}
{/if}