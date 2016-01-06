{if $oMovieObj->getAwardSet($searchEventID)->isWinner()}
	<img src="/themes/shared/icons/award_star_gold_3.png" alt="{t}Event Winner{/t}" title="{t}Event Winner{/t}" class="smallIcon" />
{elseif $oMovieObj->getAwardSet($searchEventID)->isFinalist()}
	{assign var=oAward value=$oMovieObj->getAwardSet($searchEventID)->getBestAwardResultByType(mofilmMovieAward::TYPE_FINALIST)}
	<div class="finalistIcon" title="{t}Event Finalist{/t}"><span>{$oAward->getPosition()}</span></div>
{elseif $oMovieObj->getAwardSet($searchEventID)->isRunnerUp()}
	<img src="/themes/shared/icons/medal_silver_1.png" alt="{t}Event Runner Up{/t}" title="{t}Event Runner Up{/t}" class="smallIcon" />
{elseif $oMovieObj->getAwardSet($searchEventID)->isShortlisted()}
	<img src="/themes/shared/icons/medal_bronze_1.png" alt="{t}Shortlisted for Event{/t}" title="{t}Shortlisted for Event{/t}" class="smallIcon" />
{/if}
<a href="{adminMovieLink movieID=$oMovieObj->getID()}" title="{t}Watch this movie{/t}">
	{$oMovieObj->getTitle()}
</a>