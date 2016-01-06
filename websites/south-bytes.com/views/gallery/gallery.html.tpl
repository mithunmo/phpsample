{include file=$oView->getTemplateFile('header','/shared') pageTitle="SOUTHBYTES the official video voice of SXSW - Gallery"}
{include file=$oView->getTemplateFile('menu','/shared') selected="gallery"}


	<div id="gallery">
		<h2>Newest Additions</h2>
		{if $oLatest->getTotalResults() > 0}
			{foreach $oLatest as $oMovie}
			<div class="movie">
				<a href="/watch/{$oMovie->getShortUri(0)}" title="{$oMovie->getTitle()|xmlstring}"><img src="{$oMovie->getThumbnailUri('s')}" alt="Movie: {$oMovie->getTitle()|xmlstring}" class="movieThumbnail" /></a>
			</div>
			{/foreach}

			<div class="clear"></div>
		{else}
			<p>Whoops! We haven't had any submissions yet, why not be the first?!</p>
			<p>Check the <a href="/static/terms">Terms and Conditions</a> of entry and get <a href="http://www.mofilm.com/account/upload?sourceID=138">uploading</a>.</p>
		{/if}

		{if $oTopRated->getTotalResults() > 0}
			<h2>Top 5 Highest Rated</h2>
			{foreach $oTopRated as $oMovie}
			<div class="movie">
				<a href="/watch/{$oMovie->getShortUri(0)}" title="{$oMovie->getTitle()|xmlstring}"><img src="{$oMovie->getThumbnailUri('s')}" alt="Movie: {$oMovie->getTitle()|xmlstring}" class="movieThumbnail" /></a>
			</div>
			{/foreach}

			<div class="clear"></div>
		{/if}
	</div>


{include file=$oView->getTemplateFile('footer','/shared')}