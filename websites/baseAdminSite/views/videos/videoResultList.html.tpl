<table class="data">
	<thead>
		<tr>
			<th class="first">{t}MovieID{/t}</th>
			<th style="width: 55px;">{t}Event{/t}</th>
			<th style="width: 55px;">{t}Source{/t}</th>
			<th style="width: 55px;">{t}Thumb{/t}</th>
                        <th style="width: 30px;">
                         <a href="{strip}
					{$daoUriView}?OrderBy={mofilmMovieSearch::ORDERBY_AWARD}&amp;
					OrderDir={if $searchOrderBy != mofilmMovieSearch::ORDERBY_AWARD || $searchOrderDir == mofilmMovieSearch::ORDER_ASC}2{else}1{/if}
					{if $rawDaoSearchQuery}&amp;{$rawDaoSearchQuery}{/if}
				{/strip}" title="{t}Click to sort by movie AWard {if $searchOrderBy != mofilmMovieSearch::ORDERBY_AWARD || $searchOrderDir == mofilmMovieSearch::ORDER_ASC}highest to lowest{else}lowest to highest{/if}{/t}">
					
                                        {t}Award{/t}
					{if $searchOrderBy == mofilmMovieSearch::ORDERBY_AWARD}
						<img src="{$themeicons}/16x16/view-sort-{if $searchOrderDir == mofilmMovieSearch::ORDER_ASC}descending{else}ascending{/if}.png" alt="" class="smallIcon" />
					{/if}
			</a>		                                                                                                                                             
                            
                        
                        </th>
			<th>
                         <a href="{strip}
					{$daoUriView}?OrderBy={mofilmMovieSearch::ORDERBY_TITLE}&amp;
					OrderDir={if $searchOrderBy != mofilmMovieSearch::ORDERBY_TITLE || $searchOrderDir == mofilmMovieSearch::ORDER_ASC}2{else}1{/if}
					{if $rawDaoSearchQuery}&amp;{$rawDaoSearchQuery}{/if}
				{/strip}" title="{t}Click to sort by movie Title {if $searchOrderBy != mofilmMovieSearch::ORDERBY_TITLE || $searchOrderDir == mofilmMovieSearch::ORDER_ASC}highest to lowest{else}lowest to highest{/if}{/t}">
					
					{t}Title{/t}
					{if $searchOrderBy == mofilmMovieSearch::ORDERBY_TITLE}
						<img src="{$themeicons}/16x16/view-sort-{if $searchOrderDir == mofilmMovieSearch::ORDER_ASC}descending{else}ascending{/if}.png" alt="" class="smallIcon" />
					{/if}
			</a>		                                                                                                                                             
                        </th>
			<th style="width: 75px;">
				<a href="{strip}
					{$daoUriView}?OrderBy={mofilmMovieSearch::ORDERBY_DATE}&amp;
					OrderDir={if $searchOrderDir == mofilmMovieSearch::ORDER_ASC}2{else}1{/if}
					{if $rawDaoSearchQuery}&amp;{$rawDaoSearchQuery}{/if}
				{/strip}" title="{t}Click to sort by date {if $searchOrderDir == mofilmMovieSearch::ORDER_ASC}newest to oldest{else}oldest to newest{/if}{/t}">
				
					{t}Uploaded{/t}
					{if $searchOrderBy == mofilmMovieSearch::ORDERBY_DATE}
						<img src="{$themeicons}/16x16/view-sort-{if $searchOrderDir == mofilmMovieSearch::ORDER_ASC}descending{else}ascending{/if}.png" alt="" class="smallIcon" />
					{/if}
				</a>                            
			</th>
			<th>{t}Status{/t}</th>
			<th style="width: 60px;">
				<a href="{strip}
					{$daoUriView}?OrderBy={mofilmMovieSearch::ORDERBY_RATING}&amp;
					OrderDir={if $searchOrderBy != mofilmMovieSearch::ORDERBY_RATING || $searchOrderDir == mofilmMovieSearch::ORDER_ASC}2{else}1{/if}
					{if $rawDaoSearchQuery}&amp;{$rawDaoSearchQuery}{/if}
				{/strip}" title="{t}Click to sort by rating {if $searchOrderBy != mofilmMovieSearch::ORDERBY_RATING || $searchOrderDir == mofilmMovieSearch::ORDER_ASC}highest to lowest{else}lowest to highest{/if}{/t}">
					
					{t}Rating{/t}
					{if $searchOrderBy == mofilmMovieSearch::ORDERBY_RATING}
						<img src="{$themeicons}/16x16/view-sort-{if $searchOrderDir == mofilmMovieSearch::ORDER_ASC}descending{else}ascending{/if}.png" alt="" class="smallIcon" />
					{/if}
				</a>
			</th>
			<th class="last" style="width: 160px;">
		<a href="{strip}
					{$daoUriView}?OrderBy={mofilmMovieSearch::ORDERBY_FMNAME}&amp;
					OrderDir={if $searchOrderBy != mofilmMovieSearch::ORDERBY_FMNAME || $searchOrderDir == mofilmMovieSearch::ORDER_ASC}2{else}1{/if}
					{if $rawDaoSearchQuery}&amp;{$rawDaoSearchQuery}{/if}
				{/strip}" title="{t}Click to sort by User first name {if $searchOrderBy != mofilmMovieSearch::ORDERBY_FMNAME || $searchOrderDir == mofilmMovieSearch::ORDER_ASC}highest to lowest{else}lowest to highest{/if}{/t}">
					
					{t}User{/t}
					{if $searchOrderBy == mofilmMovieSearch::ORDERBY_FMNAME}
						<img src="{$themeicons}/16x16/view-sort-{if $searchOrderDir == mofilmMovieSearch::ORDER_ASC}descending{else}ascending{/if}.png" alt="" class="smallIcon" />
					{/if}
				</a>		            
                                        </th>
		</tr>
	</thead>
	<tfoot>
		{include file=$oView->getTemplateFile('daoSolrPaging', '/shared') colspan=8}
	</tfoot>
	<tbody>
		{include file=$oView->getTemplateFile('daoSolrPaging', '/shared') colspan=8}
		{if $oResults->getTotalResults() > 0}
			{foreach $oResults as $oVideoResult}

				{assign var=oMovieObj $oModel->getMovieByID($oVideoResult->s_id)}	

				<tr class="{cycle values=",alt"} {$oVideoResult->s_status|replace:' ':''|lower} {if $oController->hasAuthority('videosController.rate') && $oUser->getParamSet()->getParam('FlagUnratedMovies', 0)}unrated{/if}">
					<td>
						{include file=$oView->getTemplateFile('addToFavourites','videos') oMovie=$oMovieObj}
						{$oVideoResult->s_id}
					</td>
					<td class="alignCenter"><img src="{$adminEventFolder}/{$oMovieObj->getSource()->getEvent()->getLogoName()}.jpg" width="50" height="28" alt="{$oVideoResult->s_eventname}" title="{$oVideoResult->s_eventname}" class="eventLogo valignMiddle" /></td>
					<td class="alignCenter"><img src="{$adminSourceFolder}/{$oMovieObj->getSource()->getLogoName()}.jpg" width="50" height="28" alt="{$oVideoResult->s_sourcename}" title="{$oVideoResult->s_sourcename}" class="sourceLogo valignMiddle" /></td>

					<td class="alignCenter"><a href="{adminMovieLink movieID=$oVideoResult->s_id}" title="{t}Watch this movie{/t}"><img src="{$oMovieObj->getThumbnailUri('s')}" width="50" height="28" alt="Thumb" class="valignMiddle" /></a></td>
					<td>{*$oVideoResult->s_shortDesc*}
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
                                        </td>       
                                        <td>        
						<a href="{adminMovieLink movieID=$oMovieObj->getID()}" title="{t}Watch this movie{/t}">
							{$oVideoResult->s_shortDesc|xmlstring}
						</a>			


					</td>
					<td>{$oVideoResult->s_uploaded|date_format:"%e %b %y"}</td>
					<td class="alignCenter {$oVideoResult->s_status|replace:' ':''|lower}">{$oVideoResult->s_status}</td>
					<td class="alignCenter">{if $oMovieObj->getAvgRating()}{if $oMovieObj->getRatingCount() >= 1}<span class="rating rating{$oMovieObj->getAvgRating()}">{$oMovieObj->getAvgRating()}</span>{else}Not Enough Ratings{/if}{else}Unrated{/if}</td>
					<td>
						{strip}
							{if $oMovieObj->getUser()->getID() > 0}
								<a href="{$daoUriView}?UserID={$oMovieObj->getUser()->getID()}" title="{t}Search for other movies by this user{/t}">
									<img src="{$themeicons}/16x16/user-list-videos.png" alt="user videos" class="smallIcon" />
								</a>
							{/if}
						{/strip}
						{if $oController->hasAuthority('usersController.edit')}
							{if $oMovieObj->getUser()->getID() > 0}
								<a href="{system::getConfig()->getParam('mofilm', 'platformUri')->getParamValue()}{'users/'}{$oMovieObj->getUser()->getID()}{'?token='}{$accessToken}" title="{t}Edit user{/t} {$oMovieObj->getUser()->getFullname()|xmlstring}">{$oMovieObj->getUser()->getFullname()|truncate:20:'..'|xmlstring}</a>
							{else}
								{$oMovieObj->getUser()->getFullname()|truncate:20:'..'|xmlstring}
							{/if}
						{else}
							<span title="{$oMovieObj->getUser()->getFullname()|xmlstring}">{$oMovieObj->getUser()->getFullname()|truncate:20:'..'|xmlstring}</span>
						{/if}						
					</td>
				</tr>
			{/foreach}
		{else}
			<tr>
				<td colspan="9">{t}No objects found matching search criteria.{/t}</td>
			</tr>
		{/if}
	</tbody>
</table>