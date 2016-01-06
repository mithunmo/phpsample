<table class="data">
	<tbody>
		{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=1}
	</tbody>
</table>

<div class="videoGrid">
	{if $oResults->getTotalResults() > 0}
		{foreach $oResults as $oVideoResult}
		<div class="video" id="{$oVideoResult->getStatus()}">
			<div class="idHolder">
				{include file=$oView->getTemplateFile('addToFavourites','videos') oMovie=$oVideoResult}
				<span class="movieID">MovieID: {$oVideoResult->getID()}</span>
			</div>
		    <div class="thumbnail">
		    	<a href="{adminMovieLink movieID=$oVideoResult->getID()}" title="{t}Watch this movie{/t}">
		    		<img src="{$oVideoResult->getThumbnailUri('m')}" alt="movie thumbnail" width="185" height="104" />
		    	</a>
		    </div>
		    <div class="flags">
		    	<img src="{$adminEventFolder}/{$oVideoResult->getSource()->getEvent()->getLogoName()}.jpg" width="50" height="28" alt="{$oVideoResult->getSource()->getEvent()->getName()}" title="{$oVideoResult->getSource()->getEvent()->getName()}" />
				<img src="{$adminSourceFolder}/{$oVideoResult->getSource()->getLogoName()}.jpg" width="50" height="28" alt="{$oVideoResult->getSource()->getName()}" title="{$oVideoResult->getSource()->getName()}" />
		    </div>
		    <div class="title">{include file=$oView->getTemplateFile('resultComTitle')}</div>
		    <div class="user">{include file=$oView->getTemplateFile('resultComUser')}</div>
    	</div>
    	{/foreach}
    {else}
    	<p>{t}No objects found matching search criteria.{/t}</p>
    {/if}
</div>
<div class="clearBoth"></div>

<table class="data">
	<tfoot>
		{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=1}
	</tfoot>
</table>