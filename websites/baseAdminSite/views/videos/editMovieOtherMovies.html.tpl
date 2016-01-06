<h3>{$title|default:'{t}Other Videos in this Competition by this Filmmaker{/t}'}</h3>
{assign var=oResult value=$oMovie->getOtherEventMovies(0, 5, $oUser->getSeed())}
{if $oResult->getResultCount() > 0}
	<dl class="profileVideos">
	{foreach $oResult as $oRelMovie}
		<dt><a href="{adminMovieLink movieID=$oRelMovie->getID()}" class="status{$oRelMovie->getStatus()|replace:' ':''}" title="{$oRelMovie->getShortDesc()|xmlstring} ({$oRelMovie->getStatus()})"><img src="{$oRelMovie->getThumbnailUri('s')}" class="thumb" height="84" width="150" /></a></dt>
		<dd>
			#{$oRelMovie->getID()} <a href="{adminMovieLink movieID=$oRelMovie->getID()}" title="{t}Go to{/t} {$oRelMovie->getShortDesc()|escape:'html':'UTF-8'}">{$oRelMovie->getShortDesc()|truncate:30:'..'|escape:'html':'UTF-8'}</a>
			<div class="alignRight">
				<div class="floatLeft">{t}Duration: {/t} {$oRelMovie->getDuration()|convertSecondsToMinutes}</div>
				{if $oUser->isAuthorised('setStatus') && ($oRelMovie->getStatus() == mofilmMovie::STATUS_PENDING || $oUser->getPermissions()->isRoot())}
					<a href="{$statusURI}/{$oRelMovie->getID()}/Rejected" title="{t}Reject Movie{/t}" class="ajaxRejectUpdate">{t}Reject{/t}</a>
				{/if}
				<div class="clearBoth"></div>
			</div>
		</dd>
	{/foreach}
	</dl>
	{if $oResult->getTotalResults() > $oResult->getResultCount()}
		{if $oController->hasAuthority('videosController.doSearch')}
			<p class="alignRight noMargin"><a href="/videos/doSearch?UserID={$oRelMovie->getUserID()}&amp;EventID={$oRelMovie->getSource()->getEventID()}&amp;SourceID=0" title="{t}Show all other movies from this filmmaker in this competition{/t}">More &raquo;</a></p>
		{/if}
	{/if}
{else}
	<p>{$novideos|default:'{t}There are no other videos by this filmmaker.{/t}'}</p>
{/if}