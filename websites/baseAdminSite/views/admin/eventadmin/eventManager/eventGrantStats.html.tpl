{if $grantStats->getArrayCount() > 0}
	<table class="data smallText">
		<thead>
			<tr>
				<th style="width: 300px;">Brand</th>
				<th>Approved</th>
				<th>Rejected</th>
				<th>Pending</th>
				<th>Total Grant Applications</th>
			</tr>
		</thead>
		<tbody>
	{foreach $grantStats as $oStats}
		{if $oStats->getOptionsSet()->getCount() > 0}
			<tr class="{cycle values='alt,'}">
				<th>{$oStats->getSource()->getName()|xmlstring}</th>
				<td class="alignCenter">{$oStats->getTotalApproved()}</td>
				<td class="alignCenter">{$oStats->getTotalRejected()}</td>
				<td class="alignCenter">{$oStats->getTotalPending()}</td>
				<td class="alignCenter">{$oStats->getTotalGrants()}</td>
			</tr>
		{/if}
	{/foreach}
		</tbody>
	</table>
{else}
	{t}No grants statistics found for the event.{/t}
{/if}