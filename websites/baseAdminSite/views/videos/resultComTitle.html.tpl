{if $oVideoResult->getAwardSet($searchEventID)->isWinner()}
	<img src="/themes/shared/icons/award_star_gold_3.png" alt="{t}Event Winner{/t}" title="{t}Event Winner{/t}" class="smallIcon" />
{elseif $oVideoResult->getAwardSet($searchEventID)->isFinalist()}
	{assign var=oAward value=$oVideoResult->getAwardSet($searchEventID)->getBestAwardResultByType(mofilmMovieAward::TYPE_FINALIST)}
	<div class="finalistIcon" title="{t}Event Finalist{/t}"><span>{$oAward->getPosition()}</span></div>
{elseif $oVideoResult->getAwardSet($searchEventID)->isRunnerUp()}
	<img src="/themes/shared/icons/medal_silver_1.png" alt="{t}Event Runner Up{/t}" title="{t}Event Runner Up{/t}" class="smallIcon" />
{elseif $oVideoResult->getAwardSet($searchEventID)->isShortlisted()}
	<img src="/themes/shared/icons/medal_bronze_1.png" alt="{t}Shortlisted for Event{/t}" title="{t}Shortlisted for Event{/t}" class="smallIcon" />
{/if}
