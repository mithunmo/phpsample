<p>
	<a href="http://bcsff.mofilm.cn/" title="MOFILM北京大学生电影节竞赛单元"><img src="/themes/mofilm/images/mofilmcn/bcsfficon.jpg" alt="bcsff" ></a>
</p>
<h3>{$title|default:'{t}Your 5 Most Recent Videos{/t}'}</h3>
{assign var=oResult value=$oUser->getUserMovies()}
{if $oResult->getResultCount() > 0}
	<dl class="profileVideos">
	{foreach $oResult as $oMovie}
		<dt><img src="{$oMovie->getThumbnailUri('s')}" class="thumb" /></dt>
		<dd><a href="{$oMovie->getShortUri($oMovie->getUserID(),true)}" title="{t}Watch {/t}{$oMovie->getShortDesc()|xmlstring}">{$oMovie->getShortDesc()|truncate:25:'..'|escape:'html':'UTF-8'}</a></dd>
	{/foreach}
	</dl>
{else}
	<p>{$novideos|default:'{t}You have not uploaded any videos yet.{/t}'}</p>
{/if}
