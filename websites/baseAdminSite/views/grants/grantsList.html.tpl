{assign var=offset value=$pagingOffset|default:0}
{assign var=limit value=30}
{assign var=totalObjects value=$oResults->getTotalResults()}

{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}User Grants List{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}
<div id="body">
	<div class="container">
	    		<h2>{t}Ideas / Grants{/t}</h2>

			<form name="grantSearch" action="{$daoUriView}" method="get">
				<div class="filters">
					<p style="margin: 0; margin-bottom: 5px;">
						{eventSelect id="eventList" name='EventID' selected=$searchEventID class="valignMiddle string" user=$oUser}
						{if $searchEventID}
							{sourceSelect id="eventListSources" name='SourceID' selected=$searchSourceID eventID=$searchEventID class="valignMiddle string" user=$oUser}
						{else}
							{sourceDistinctSelect id="eventListSources" name='SourceID' selected=$searchSourceID class="valignMiddle string" user=$oUser}
						{/if}
						{movieStatusSelect name='Status' selected=$searchStatus class="valignMiddle"}
					</p>
                                        <button type="submit" name="buttonname" value="{t}PDF{/t}"  id="genPdf">
						<img src="{$themeicons}/32x32/mime-application-pdf.png" alt="Generate PDF" class="icon" />
						{t}Generate PDF{/t}
					</button>
                                        

                                         
					<button type="submit" name="buttonname" value="{t}Email{/t}" class="floatRight" id="sendEmail">
						<img src="{$themeicons}/32x32/email-icon.png" alt="Email" class="icon" />
						{t}Acceptance Email{/t}
					</button>
                                        <button type="submit" name="buttonname" value="REJECT" id="RejApplication" class="floatRight">
                                            <img src="{$themeicons}/32x32/action-reject-status.png" alt="Reject Application" class="icon" />
                                                Reject Application
                                        </button>
					
					<button type="submit" name="buttonname" value="{t}Search{/t}" class="floatRight">
						<img src="{$themeicons}/32x32/search.png" alt="search" class="icon" />
						{t}Search{/t}
					</button>
					<div class="clearBoth"></div>
				</div>

			{if $oResults->getResults()->getArrayCount() > 0}
			<table class="data">
				<tfoot>
					{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=8}
				</tfoot>
				<thead>
					<tr>
						<th style="width: 20px;" class="first"><input type="checkbox" name="buttonallselect" value="1"  id="AllSelect" > &nbsp;All </th>
						<th style="width: 135px;">
                                                    <a href="{strip}
					{$daoUriView}?OrderBy={mofilmUserMovieGrantsSearch::ORDERBY_NAME}&amp;
					OrderDir={if $searchOrderBy != mofilmUserMovieGrantsSearch::ORDERBY_NAME || $searchOrderDir == mofilmUserMovieGrantsSearch::ORDER_ASC}2{else}1{/if}
					{if $rawDaoSearchQuery}&amp;{$rawDaoSearchQuery}{/if}
				{/strip}" title="{t}Click to sort by rating {if $searchOrderBy != mofilmUserMovieGrantsSearch::ORDERBY_NAME || $searchOrderDir == mofilmUserMovieGrantsSearch::ORDER_ASC}highest to lowest{else}lowest to highest{/if}{/t}">
					
					{t}User{/t}
					{if $searchOrderBy == mofilmUserMovieGrantsSearch::ORDERBY_NAME}
						<img src="{$themeicons}/16x16/view-sort-{if $searchOrderDir == mofilmUserMovieGrantsSearch::ORDER_ASC}descending{else}ascending{/if}.png" alt="" class="smallIcon" />
					{/if}
				</a>
                                                    
                                                    </th>
						<th>
                                                    <a href="{strip}
					{$daoUriView}?OrderBy={mofilmUserMovieGrantsSearch::ORDERBY_TITLE}&amp;
					OrderDir={if $searchOrderBy != mofilmUserMovieGrantsSearch::ORDERBY_TITLE || $searchOrderDir == mofilmUserMovieGrantsSearch::ORDER_ASC}2{else}1{/if}
					{if $rawDaoSearchQuery}&amp;{$rawDaoSearchQuery}{/if}
				{/strip}" title="{t}Click to sort by rating {if $searchOrderBy != mofilmUserMovieGrantsSearch::ORDERBY_TITLE || $searchOrderDir == mofilmUserMovieGrantsSearch::ORDER_ASC}highest to lowest{else}lowest to highest{/if}{/t}">
					
					{t}Working Title{/t}
					{if $searchOrderBy == mofilmUserMovieGrantsSearch::ORDERBY_TITLE}
						<img src="{$themeicons}/16x16/view-sort-{if $searchOrderDir == mofilmUserMovieGrantsSearch::ORDER_ASC}descending{else}ascending{/if}.png" alt="" class="smallIcon" />
					{/if}
				</a>
                                                    
                                                    </th>
						<th style="width: 55px;">Project </th>
						<th style="width: 55px;">Brand </th>
						<th style="width: 75px;">
                                                     <a href="{strip}
					{$daoUriView}?OrderBy={mofilmUserMovieGrantsSearch::ORDERBY_RAMT}&amp;
					OrderDir={if $searchOrderBy != mofilmUserMovieGrantsSearch::ORDERBY_RAMT || $searchOrderDir == mofilmUserMovieGrantsSearch::ORDER_ASC}2{else}1{/if}
					{if $rawDaoSearchQuery}&amp;{$rawDaoSearchQuery}{/if}
				{/strip}" title="{t}Click to sort by rating {if $searchOrderBy != mofilmUserMovieGrantsSearch::ORDERBY_RAMT || $searchOrderDir == mofilmUserMovieGrantsSearch::ORDER_ASC}highest to lowest{else}lowest to highest{/if}{/t}">
					
					{t}Requested Amount{/t}
					{if $searchOrderBy == mofilmUserMovieGrantsSearch::ORDERBY_RAMT}
						<img src="{$themeicons}/16x16/view-sort-{if $searchOrderDir == mofilmUserMovieGrantsSearch::ORDER_ASC}descending{else}ascending{/if}.png" alt="" class="smallIcon" />
					{/if}
				</a>
                                                    </th>
						<th style="width: 75px;">
                                                    <a href="{strip}
					{$daoUriView}?OrderBy={mofilmUserMovieGrantsSearch::ORDERBY_GAMT}&amp;
					OrderDir={if $searchOrderBy != mofilmUserMovieGrantsSearch::ORDERBY_GAMT || $searchOrderDir == mofilmUserMovieGrantsSearch::ORDER_ASC}2{else}1{/if}
					{if $rawDaoSearchQuery}&amp;{$rawDaoSearchQuery}{/if}
				{/strip}" title="{t}Click to sort by rating {if $searchOrderBy != mofilmUserMovieGrantsSearch::ORDERBY_GAMT || $searchOrderDir == mofilmUserMovieGrantsSearch::ORDER_ASC}highest to lowest{else}lowest to highest{/if}{/t}">
					
					{t}Granted Amount{/t}
					{if $searchOrderBy == mofilmUserMovieGrantsSearch::ORDERBY_GAMT}
						<img src="{$themeicons}/16x16/view-sort-{if $searchOrderDir == mofilmUserMovieGrantsSearch::ORDER_ASC}descending{else}ascending{/if}.png" alt="" class="smallIcon" />
					{/if}
				</a>
                                                   </th>
                                                 <th>
                                                    <a href="{strip}
					{$daoUriView}?OrderBy={mofilmUserMovieGrantsSearch::ORDERBY_RATING}&amp;
					OrderDir={if $searchOrderBy != mofilmUserMovieGrantsSearch::ORDERBY_RATING || $searchOrderDir == mofilmUserMovieGrantsSearch::ORDER_ASC}2{else}1{/if}
					{if $rawDaoSearchQuery}&amp;{$rawDaoSearchQuery}{/if}
				{/strip}" title="{t}Click to sort by rating {if $searchOrderBy != mofilmUserMovieGrantsSearch::ORDERBY_RATING || $searchOrderDir == mofilmUserMovieGrantsSearch::ORDER_ASC}highest to lowest{else}lowest to highest{/if}{/t}">
					
					{t}Rating{/t}
					{if $searchOrderBy == mofilmUserMovieGrantsSearch::ORDERBY_GAMT}
						<img src="{$themeicons}/16x16/view-sort-{if $searchOrderDir == mofilmUserMovieGrantsSearch::ORDER_ASC}descending{else}ascending{/if}.png" alt="" class="smallIcon" />
					{/if}
				</a>
                                                     
                                                 
                                                 </th>  
						<th> 
                                                    <a href="{strip}
					{$daoUriView}?OrderBy={mofilmUserMovieGrantsSearch::ORDERBY_STATUS}&amp;
					OrderDir={if $searchOrderBy != mofilmUserMovieGrantsSearch::ORDERBY_STATUS || $searchOrderDir == mofilmUserMovieGrantsSearch::ORDER_ASC}2{else}1{/if}
					{if $rawDaoSearchQuery}&amp;{$rawDaoSearchQuery}{/if}
				{/strip}" title="{t}Click to sort by rating {if $searchOrderBy != mofilmUserMovieGrantsSearch::ORDERBY_STATUS || $searchOrderDir == mofilmUserMovieGrantsSearch::ORDER_ASC}highest to lowest{else}lowest to highest{/if}{/t}">
					
					{t}Status{/t}
					{if $searchOrderBy == mofilmUserMovieGrantsSearch::ORDERBY_STATUS}
						<img src="{$themeicons}/16x16/view-sort-{if $searchOrderDir == mofilmUserMovieGrantsSearch::ORDER_ASC}descending{else}ascending{/if}.png" alt="" class="smallIcon" />
					{/if}
				</a>
                                                 
                                                   
                                                </th>
						<th class="last">Actions</th>
					</tr>
				</thead>
				<tbody>
				{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=8}
				{foreach $oResults->getResults() as $oObject}
					<tr class="{cycle values=",alt"}">
						<td {if $oObject@iteration % 2}class="alt"{/if}>
							<input type="checkbox" name="selectedpdfs[]" value="{$oObject->getID()}" id="selectedPdf" />
							{if $oObject->getStatus() == 'Approved' }
								{if $oObject->getMovieID() > 0}
									<a href="{$oObject->getMovie()->getShortUri($oObject->getUserID(), true)}" ><img src="{$oObject->getMovie()->getThumbnailUri()}" alt="{$oObject->getMovieID()}" width="50" height="28" class="valignMiddle" /></a>
								{/if}
							{/if}
						</td>
						<td {if $oObject@iteration % 2}class="alt"{/if}><a href="{system::getConfig()->getParam('mofilm', 'platformUri')->getParamValue()}{'users/'}{$oObject->getUserID()}{'?token='}{$accessToken}"> {if $oObject->getUser()->getFullname()}{$oObject->getUser()->getFullname()}{else}{$oObject->getUser()->getPropername()}{/if}  </a></td>
						<td {if $oObject@iteration % 2}class="alt"{/if}>
							{$oObject->getFilmTitle()}
							{if !($oObject->getApplicationAppliedStatus())}
								<img src="/themes/mofilm/images/past_deadline.png" width="16" height="16" alt="Applied past deadline" title="Applied past deadline" />
							{/if}
						</td>
						<td {if $oObject@iteration % 2}class="alt"{/if}><img src="/resources/client/events/logo/{$oObject->getGrants()->getSource()->getEvent()->getLogoName()}.png" width="50" height="28" alt="{$oObject->getGrants()->getSource()->getEvent()->getName()}" title="{$oObject->getGrants()->getSource()->getEvent()->getName()}" /></td>
						<td {if $oObject@iteration % 2}class="alt"{/if}><img src="/resources/client/sources/logo/{$oObject->getGrants()->getSource()->getLogoName()}.png" width="50" height="28" alt="{$oObject->getGrants()->getSource()->getName()}" title="{$oObject->getGrants()->getSource()->getName()}" /></td>
						<td {if $oObject@iteration % 2}class="alt"{/if}>{$oObject->getGrants()->getCurrencySymbol()} {$oObject->getRequestedAmount()}</td>
						<td {if $oObject@iteration % 2}class="alt"{/if}>{if $oObject->getGrantedAmount() > 0}{$oObject->getGrants()->getCurrencySymbol()} {$oObject->getGrantedAmount()}{/if}</td>
                                                <td>{$oModel->getAvgRating($oObject->getID())} </td>
						<td {if $oObject@iteration % 2}class="alt"{/if}>{$oObject->getStatus()}</td>
						<td {if $oObject@iteration % 2}class="alt"{/if}>
							<a href="/grants/view/{$oObject->getID()}">View</a>
							| <a href="/grants/edit/{$oObject->getID()}">Edit</a>
						</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
			{else}
				<p>No objects found in system.</p>
			{/if}
                         <input type="hidden" name="Offset" value="{$offset}">   
			</form>
	</div>
</div>
{include file=$oView->getTemplateFile('footer', 'shared')}
