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
				{/strip}" title="{t}Click to sort by rating {if $searchOrderBy != mofilmMovieSearch::ORDERBY_FMNAME || $searchOrderDir == mofilmMovieSearch::ORDER_ASC}highest to lowest{else}lowest to highest{/if}{/t}">
					
					{t}User{/t}
					{if $searchOrderBy == mofilmMovieSearch::ORDERBY_FMNAME}
						<img src="{$themeicons}/16x16/view-sort-{if $searchOrderDir == mofilmMovieSearch::ORDER_ASC}descending{else}ascending{/if}.png" alt="" class="smallIcon" />
					{/if}
				</a>		            
                        </th>
		</tr>
	</thead>
	<tfoot>
		{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=8}
	</tfoot>
	<tbody>
		{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=8}
		{if $oResults->getTotalResults() > 0}
		{foreach $oResults as $oVideoResult}
		<tr class="{cycle values=",alt"} {$oVideoResult->getStatus()|replace:' ':''|lower} {if $oController->hasAuthority('videosController.rate') && $oUser->getParamSet()->getParam('FlagUnratedMovies', 0)}unrated{/if}">
			<td>
				{include file=$oView->getTemplateFile('addToFavourites','videos') oMovie=$oVideoResult}
				{$oVideoResult->getID()}
			</td>
			<td class="alignCenter"><img src="{$adminEventFolder}/{$oVideoResult->getSource()->getEvent()->getLogoName()}.jpg" width="50" height="28" alt="{$oVideoResult->getSource()->getEvent()->getName()}" title="{$oVideoResult->getSource()->getEvent()->getName()}" class="eventLogo valignMiddle" /></td>
			<td class="alignCenter"><img src="{$adminSourceFolder}/{$oVideoResult->getSource()->getLogoName()}.jpg" width="50" height="28" alt="{$oVideoResult->getSource()->getName()}" title="{$oVideoResult->getSource()->getName()}" class="sourceLogo valignMiddle" /></td>
			<td class="alignCenter"><a href="{adminMovieLink movieID=$oVideoResult->getID()}" title="{t}Watch this movie{/t}"><img src="{$oVideoResult->getThumbnailUri('s')}" width="50" height="28" alt="Thumb" class="valignMiddle" /></a></td>
			<td>{include file=$oView->getTemplateFile('resultComTitle')}</td>
                        <td>
                            <a href="{adminMovieLink movieID=$oVideoResult->getID()}" title="{t}Watch this movie{/t}">{$oVideoResult->getTitle()}
                        </a></td>
			<td>{$oVideoResult->getUploaded()|date_format:"%e %b %y"}</td>
			<td class="alignCenter {$oVideoResult->getStatus()|replace:' ':''|lower}">{$oVideoResult->getStatus()}</td>
			<td class="alignCenter">{if $oVideoResult->getAvgRating()}{if $oVideoResult->getRatingCount() >= 1}<span class="rating rating{$oVideoResult->getAvgRating()}">{$oVideoResult->getAvgRating()}</span>{else}Not Enough Ratings{/if}{else}Unrated{/if}</td>
			<td>{include file=$oView->getTemplateFile('resultComUser')}</td>
		</tr>
		{/foreach}
		{else}
		<tr>
			<td colspan="9">{t}No objects found matching search criteria.{/t}</td>
		</tr>
		{/if}
	</tbody>
</table>