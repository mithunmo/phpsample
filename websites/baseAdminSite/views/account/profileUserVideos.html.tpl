<h3>{$title|default:'{t}Your Videos{/t}'}</h3>
{assign var=oResult value=$oUser->getUserMovies()}
{if $oResult->getResultCount() > 0}
	<dl class="profileVideos">
	{foreach $oResult as $oMovie}
		<dt><a href="{adminMovieLink movieID=$oMovie->getID()}"><img src="{$oMovie->getThumbnailUri('s')}" class="thumb" /></a></dt>
		<dd><a href="{adminMovieLink movieID=$oMovie->getID()}" title="{$oMovie->getShortDesc()|xmlstring}">{$oMovie->getShortDesc()|truncate:25:'..'|escape:'html':'UTF-8'}</a></dd>
	{/foreach}
	</dl>
	{if $oResult->getTotalResults() > $oResult->getResultCount()}
		{if $oController->hasAuthority('videosController.doSearch')}
			<p class="alignRight noMargin"><a href="/videos/doSearch?UserID={$oMovie->getUserID()}" title="{t}Show all other movies from this filmmaker{/t}">More &raquo;</a></p>
		{/if}
	{/if}
{else}
	<p>{$novideos|default:'{t}You have not uploaded any videos yet.{/t}'}</p>
{/if}
