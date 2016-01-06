<h3>{$title|default:'{t}Movie Stats{/t}'}</h3>
<dl class="userProfile">
	<dt><img src="/themes/shared/icons/date.png" alt="date" class="smallIcon"/></dt>
	<dd>{t}Uploaded {$oMovie->getUploaded()|date_format:"%d-%b-%y @ %H:%M"}{/t}</dd>

	<dt><img src="/themes/shared/icons/hourglass.png" alt="duration" class="smallIcon"/></dt>
	<dd>{t}Duration {$oMovie->getDuration()} secs{/t}</dd>
	{if $oMovie->getGrantsData()}
		{if $oMovie->getGrantsData()->getStatus() == 'Approved'}
			<dt><img src="/themes/shared/icons/grants_received.png" alt="grants received" class="smallIcon"/></dt>
			<dd>{t}Grants Received{/t}</dd>
		{/if}
	{/if}

	{if $oMovie->getAwardSet()->getCount() > 0}
		{foreach $oMovie->getAwardSet() as $oAward}
		<dt class="award">{strip}
			{if $oAward->isWinner()}
				<img src="/themes/shared/icons/award_star_gold_3.png" alt="{t}Event Winner{/t}" title="{t}Event Winner{/t}" class="smallIcon" />
			{elseif $oAward->isFinalist()}
				<img src="/themes/shared/icons/medal_gold_3.png" alt="{t}Event Finalist{/t} {$oAward->getPosition()}" title="{t}Event Finalist{/t} {$oAward->getPosition()}" class="smallIcon" />
			{elseif $oAward->isRunnerUp()}
				<img src="/themes/shared/icons/medal_silver_1.png" alt="{t}Event Runner Up{/t}" title="{t}Event Runner Up{/t}" class="smallIcon" />
			{elseif $oAward->isShortlisted()}
				<img src="/themes/shared/icons/medal_bronze_1.png" alt="{t}Shortlisted for Event{/t}" title="{t}Shortlisted for Event{/t}" class="smallIcon" />
                        {else if $oAward->getType() != 'BestOfClients' && $oAward->getType() != 'ProFinal' && $oAward->getType() != 'ProShowcase'} 
				<img src="/themes/shared/icons/rosette.png" alt="{$oAward->getType()}" title="{$oAward->getType()}" class="smallIcon" />
			{/if}{/strip}</dt>
		<dd class="award">
			{if $oAward->isWinner()}
				{t}Overall Event Winner{/t}
			{else if $oAward->getType() != 'BestOfClients' && $oAward->getType() != 'ProFinal' && $oAward->getType() != 'ProShowcase'}
				{$oAward->getType()|xmlstring}
			{/if}

			{if $oAward->isFinalist()}
				{strip}
				{$oAward->getPosition()}
				{if $oAward->getPosition() == 1}st{elseif $oAward->getPosition() == 2}nd{elseif $oAward->getPosition() == 3}rd{else}th{/if}
				{/strip} {t}Place{/t}
			{/if}
                        
                        
		</dd>
		{/foreach}
                {if $oAward->getType() == 'ProShowcase'}
                        <dd class="award">
                        <img src="/themes/shared/icons/rosette.png" alt="Pro Showcase" title="Pro Showcase" class="smallIcon" />
                        {t}Nominated for Pro Showcase Award{/t}
                        </dd>
                {/if}
                
                {if $oAward->getType() == 'ProFinal'}
                        <dd class="award">
                        <img src="/themes/shared/icons/rosette.png" alt="Pro Final" title="Pro Final" class="smallIcon" />
                        {t}Nominated for Pro Final Award{/t}
                        </dd>
                {/if}

                {if !$awardBestOfClient}
                        <dd class="award">
                        <img src="/themes/shared/icons/rosette.png" alt="Best Of Clients" title="Best Of Clients" class="smallIcon" />
                        {t}Nominated for Best Of Clients Award{/t}
                        </dd>
                {/if}
               
	{/if}
</dl>