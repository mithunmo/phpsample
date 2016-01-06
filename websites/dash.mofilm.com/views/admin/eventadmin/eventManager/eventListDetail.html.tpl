<div class="event collapsed">
	<div class="eventIcon">
		<img src="{$clientEventFolder}/{$oEvent->getLogoName()}.jpg" alt="{$oEvent->getName()}" />
	</div>
	<div class="eventDetails">
		<div class="eventTitle">
			<h3>{$oEvent->getName()}</h3>
			{if $oEvent->getStartDate()}<p><em>({$oEvent->getStartDate()|date_format:"%d/%m/%y"} - {$oEvent->getEndDate()|date_format:"%d/%m/%y"})</em></p>{/if}
		</div>
	</div>
	{assign var=oStats value=$oEvent->getStats($oUser->getSeed())}

	<div class="stats">
		{if $oStats->getOptionsSet()->getCount() > 0}
			<table class="data">
				<thead>
					<tr>
						<th>{t}Total Vids{/t}</th>
						<th>App</th>
						<th>Rej</th>
						<th>Pen</th>
						<th>Briefs</th>
					</tr>
				</thead>
				<tbody>
					<tr class="alt">
						<th>{$oStats->getTotalMovies()}</th>
						<td>{$oStats->getOption('Approved', 0)}</td>
						<td>{$oStats->getOption('Rejected', 0)}</td>
						<td>{$oStats->getOption('Pending', 0)}</td>
						<td>{$oStats->getOption('Brief Downloads', 0)}</td>
					</tr>
				</tbody>
			</table>
		{/if}
		{if $oController->hasAuthority('canSeeSourceStats')}
			<div class="sourceStatsContainer">
				<div id="event_{$oEvent->getID()}" class="sourceStats">{t}View Source Stats{/t}</div>
				<div class="sourceStatsResults"></div>
			</div>
		{/if}
	</div>

	<div class="clearBoth"></div>
</div>