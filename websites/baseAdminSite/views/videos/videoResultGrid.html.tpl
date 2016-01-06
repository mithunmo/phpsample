<table class="data">
	<tbody>
		{include file=$oView->getTemplateFile('daoSolrPaging', '/shared') colspan=1}
	</tbody>
</table>

<div class="videoGrid">
	{if $oResults->getTotalResults() > 0}
		{foreach $oResults as $oVideoResult}
		{assign var=oMovieObj $oModel->getMovieByID($oVideoResult->s_id)}	
	
		<div class="video" id="{$oMovieObj->getStatus()}">
			<div class="idHolder">
				{include file=$oView->getTemplateFile('addToFavourites','videos') oMovie=$oMovieObj}
				<span class="movieID">MovieID: {$oVideoResult->s_id}</span>
			</div>
		    <div class="thumbnail">
		    	<a href="{adminMovieLink movieID=$oVideoResult->s_id}" title="{t}Watch this movie{/t}">
		    		<img src="{$oMovieObj->getThumbnailUri('m')}" alt="movie thumbnail" width="185" height="104" />
		    	</a>
		    </div>
		    <div class="flags">
		    	<img src="{$adminEventFolder}/{$oMovieObj->getSource()->getEvent()->getLogoName()}.jpg" width="50" height="28" alt="{$oMovieObj->getSource()->getEvent()->getName()}" title="{$oMovieObj->getSource()->getEvent()->getName()}" />
				<img src="{$adminSourceFolder}/{$oMovieObj->getSource()->getLogoName()}.jpg" width="50" height="28" alt="{$oMovieObj->getSource()->getName()}" title="{$oMovieObj->getSource()->getName()}" />
		    </div>
		    <div class="title">{include file=$oView->getTemplateFile('videoResultComTitle')}</div>
		    <div class="user">{include file=$oView->getTemplateFile('videoResultComUser')}</div>
    	</div>
    	{/foreach}
    {else}
    	<p>{t}No objects found matching search criteria.{/t}</p>
    {/if}
</div>
<div class="clearBoth"></div>

<table class="data">
	<tfoot>
		{include file=$oView->getTemplateFile('daoSolrPaging', '/shared') colspan=1}
	</tfoot>
</table>