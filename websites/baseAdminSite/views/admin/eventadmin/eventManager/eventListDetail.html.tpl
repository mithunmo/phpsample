<div id="event_{$oEvent->getID()}" class="event {if $oUser->getEventFavourites()->isFavourite($oEvent->getID())}removeBookmark{else}bookmark{/if} {if $collapseOldEvents && $oEvent->getEndDate() < $eventMaxAge}collapsed{/if}">
	<div class="eventIcon">
		<a href="/videos/doSearch?EventID={$oEvent->getID()}">
			<img src="{$clientEventFolder}/logo/{$oEvent->getLogoName()}.png" alt="{$oEvent->getName()}" />
		</a>
	</div>
	<div class="eventDetails">
		<div class="eventTitle">
			<h3>
				{$oEvent->getName()}
				{if $oEvent->getStartDate()}<em>({$oEvent->getStartDate()|date_format:"%d/%m/%y"} - {$oEvent->getEndDate()|date_format:"%d/%m/%y"})</em>{/if}
                                <span style="float:right;padding-right: 10px;"> <em> {mofilmProduct::getInstance($oEvent->getProductID())->getName()}</em> </span>				
			</h3>
                        <h3>
				
			</h3>

                        
		</div>
	</div>
	{assign var=oStats value=$oEvent->getStats($oUser->getSeed())}
	<ul class="stats">
		<li>
			{$oStats->getTotalMovies()} <a href="/videos/doSearch?EventID={$oEvent->getID()}">{t}Total Videos{/t}</a>
			{if $oStats->getOptionsSet()->getCount() > 0}
			<ul>
				<li>
					{foreach $oStats->getOptionsSet() as $stat => $value}
					<span>{$value} <a href="/videos/doSearch?Status={$stat|escape:'url'}&amp;EventID={$oEvent->getID()}">{$stat}</a></span>{if $value@iteration != $oStats->getOptionsSet()->getCount()},{/if}
					{/foreach}
				</li>
			</ul>
			{/if}
		</li>
		{if $oController->hasAuthority('canSeeSourceStats')}
		<li>
			<span id="event_{$oEvent->getID()}" class="sourceStats">{t}View Source Stats{/t}</span> |
			<span id="event_{$oEvent->getID()}" class="grantStats">{t}View Grants Stats{/t}</span>
			<span class="stats sourceStatsResults"></span>
			<span class="stats grantStatsResults"></span>
		</li>
		{/if}
	</ul>
	<div class="clearBoth"></div>
</div>