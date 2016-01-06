{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Videos - Judging Mode{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			{assign var=offset value=$oResults->getSearchInterface()->getOffset()|default:0}
			{assign var=limit value=$oResults->getSearchInterface()->getLimit()}
			{assign var=totalObjects value=$oResults->getTotalResults()}
			
			{if $totalObjects == 0}
				<div class="moderationControls">
					<a href="javascript:history.go(-1);" title="{t}Previous Page{/t}" class="floatLeft"><img src="{$themeicons}/32x32/action-back.png" alt="back" class="icon" /> {t}Previous Page{/t}</a>
					
					<form id="reviewSearch" name="reviewSearch" action="{$daoUriView}{if $daoSearchQuery}?{$daoSearchQuery}{/if}">
						<span class="moderationStatus">
							<em>{$oResults->getTotalResults()}</em> <strong>{t}Outstanding movies to moderate / rate{/t}</strong>
							{if $oUser->isAuthorised('videosController.canSearchByEvent')}
								in {eventSelect name="EventID" selected=$daoSearchArray.EventID title='{t}All Viewable Events{/t}' onchange="this.form.submit()" user=$oUser exclude=true}
							{/if}
						</span>
					</form>
					
					<div class="clearBoth"></div>
				</div>
				<div class="content">
					<p>{t}There are no movies to be moderated for the selected event(s).{/t}</p>
				</div>
			{else}
				{assign var=nextOffset value=$oResults->getInstanceIndex($oMovie->getID())}
			
				<div class="moderationControls">
					<a href="javascript:history.go(-1);" title="{t}Previous Page{/t}" class="floatLeft"><img src="{$themeicons}/32x32/action-back.png" alt="back" class="icon" /> {t}Previous Page{/t}</a>
					
					<form id="reviewSearch" name="reviewSearch" action="{$daoUriView}{if $daoSearchQuery}?{$daoSearchQuery}{/if}">
						<span class="moderationStatus">
							<em>{$oResults->getTotalResults()}</em> <strong>{t}Outstanding movies to moderate / rate{/t}</strong>
							{if $oUser->isAuthorised('videosController.canSearchByEvent')}
								in {eventSelect name="EventID" selected=$daoSearchArray.EventID title='{t}All Viewable Events{/t}' onchange="this.form.submit()" user=$oUser order='enddate' exclude=true}
							{/if}
						</span>
					</form>
					
					{if $totalObjects > 0 && $offset+$nextOffset+1 < $totalObjects}
					<a href="{$daoUriView}?Offset={$offset+$nextOffset+1}&amp;Limit={$limit}{if $daoSearchQuery}&amp;{$daoSearchQuery}{/if}" title="{t}Next Video{/t}" class="floatRight">{t}Next Video{/t} <img src="{$themeicons}/32x32/action-next.png" alt="next" class="icon" /></a>
					{/if}
					<div class="clearBoth"></div>
				</div>
				
				<div class="floatLeft movieDetails">
					<div class="content">
						<div class="movieEvent floatLeft">
							<span class="imgWrap"><img src="{$adminEventFolder}/{$oMovie->getSource()->getEvent()->getLogoName()}.jpg" width="50" height="28" alt="{$oMovie->getSource()->getEvent()->getName()}" title="{t}Event: {/t}{$oMovie->getSource()->getEvent()->getName()}" class="valignMiddle" /></span>
							<span class="imgWrap"><img src="{$adminSourceFolder}/{$oMovie->getSource()->getLogoName()}.jpg" width="50" height="28" alt="{$oMovie->getSource()->getName()}" title="{t}Source: {/t}{$oMovie->getSource()->getName()}" class="valignMiddle" /></span>
						</div>
						<div class="movieTools floatRight">
							{if $oController->hasAuthority('usersController.message')}
								<a href="/users/message/{$oMovie->getUserID()}?MovieID={$oMovie->getID()}" title="{t}Message User{/t}" class="reviewButton"><img src="{$themeicons}/16x16/action-send.png" alt="{t}Message User{/t}" class="smallIcon" /> {t}Send Message{/t}</a>
							{/if}
							{if $oController->hasAuthority('videosController.edit')}
								<a href="{$editURI}/{$oMovie->getID()}" title="{t}Edit this movie{/t}" class="reviewButton"><img src="{$themeicons}/16x16/action-edit-object.png" alt="edit" class="smallIcon" /> {t}Edit movie{/t}</a>
							{/if}
							
							{if $oUser->isAuthorised('setStatus') && ($oMovie->getStatus() == mofilmMovie::STATUS_PENDING || $oUser->getPermissions()->isRoot())}
								{strip}{if $oMovie->getStatus() == mofilmMovie::STATUS_APPROVED}
									<span class="statusUpdate approve"><img src="/themes/shared/icons/tick.png" alt="approved" class="smallIcon" /> {t}Approved{/t}</span>
								{else}
									<a href="{$statusURI}/{$oMovie->getID()}/Approved" title="{t}Approve Movie{/t}" class="statusUpdate approve"><img src="/themes/shared/icons/tick.png" alt="approved" class="smallIcon" /> {t}Approve{/t}</a>
								{/if}{/strip}
								
								<a href="{$statusURI}/{$oMovie->getID()}/Rejected" title="{t}Reject Movie{/t}" class="statusUpdate reject">{t}Reject{/t}</a>
							{else}
								<span class="statusUpdate {strip}
								{if $oMovie->getStatus() == mofilmMovie::STATUS_APPROVED}
									approve
								{elseif $oMovie->getStatus() == mofilmMovie::STATUS_REJECTED}
									reject
								{else}
									pending
								{/if}
								{/strip}">{$oMovie->getStatus()}</span>
							{/if}
						</div>
						<div class="clearBoth"></div>
					
						<h2 class="noMargin">{$oMovie->getID()}: {$oMovie->getTitle()}</h2>
						
						<div class="clearBoth"></div>
						
						<div class="mofilmMovieFrame">
							<div id="mofilmMoviePlayer"></div>
						</div>
						
						{if $oController->hasAuthority('canRateVideos')}
							{assign var=oUsrRating value=$oMovie->getUserRating($oUser->getID())}
							<form id="mofilmAverageRating" class="floatLeft spacer">
								{t}Avg Rating:{/t} (<span id="mofilmMovieAverageRatingCount">{$oMovie->getRatingCount()}</span> {t}ratings{/t})<br />
								<div id="mofilmMovieAverageRating">
									{for $i=0; $i<=10; $i++}
									<input type="radio" name="Rating" value="{$i}" {if $i == $oMovie->getAvgRating()}checked="checked"{/if} />
									{/for}
								</div>
							</form>
							
							<form id="movieRatingForm" class="floatRight spacer" action="{$rateURI}" method="post">
								<input type="hidden" id="MasterMovieID" name="MovieID" value="{$oMovie->getID()}" />
								{t}Your Rating:{/t}<br />
								<div id="mofilmMovieRating">
									{for $i=0; $i<=10; $i++}
									<input type="radio" name="Rating" value="{$i}" {if $i == $oUsrRating->getRating()}checked="checked"{/if} />
									{/for}
								</div>
							</form>
						{/if}
						
						<br class="clearBoth" />
							
						<div id="userFormAccordion">
							{if $oController->hasAuthority('canSeeReviewHistory')}
								{include file=$oView->getTemplateFile('editMovieReview')}
							{/if}
							
							{if $oController->hasAuthority('canComment')}
								{include file=$oView->getTemplateFile('editMovieComments')}
							{/if}
							
							{if $oController->hasAuthority('canSeeMessageHistory')}
								{include file=$oView->getTemplateFile('editMovieMessageHistory')}
							{/if}
							
							{if $oUser->isAuthorised('canSeeModerationStatus')}
								<h3><a href="#">{t}Moderation Status{/t}</a></h3>
								<div>
									<table class="data">
										<thead>
											<tr>
												<th style="width: 200px">{t}Moderated By:{/t}</th>
												<th>{t}Comments:{/t}</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td class="valignTop">{if $oMovie->getModeratorID()}{$oMovie->getModerator()->getFullname()|xmlstring}{else}{t}Not Moderated{/t}{/if}</td>
												<td>{if $oMovie->getModeratorComments()}{$oMovie->getModeratorComments()|xmlstring}{/if}</td>
											</tr>
										</tbody>
									</table>
									
									{if $oUser->isAuthorised('setStatus') && (!$oMovie->getModeratorComments() && $oMovie->getModeratorID() == $oUser->getID() || !$oMovie->getModeratorID())}
										<form action="{$doModerationCommentUri}" method="post" id="moderationCommentForm">
											<input type="hidden" name="MovieID" value="{$oMovie->getID()}" />
											<table class="data">
												<tfoot>
													<tr>
														<td colspan="2" class="alignRight">
															<button type="reset" name="reset" value="{t}Reset{/t}">
																<img src="{$themeicons}/32x32/action-undo.png" alt="{t}Reset{/t}" class="icon" />
																{t}Reset{/t}
															</button>
															
															<button id="moderationCommentSave" type="submit" name="save" value="{t}Save{/t}">
																<img src="{$themeicons}/32x32/action-do-edit-object.png" alt="{t}Save{/t}" class="icon" />
																{t}Save{/t}
															</button>
														</td>
													</tr>
												</tfoot>
												<tbody>
													<tr>
														<th>{t}Comment{/t}</th>
														<td><textarea name="ModComment" rows="10" cols="60" class="long"></textarea></td>
													</tr>
												</tbody>
											</table>
										</form>
									{/if}
								</div>
							{/if}
						</div>
					</div>
				</div>
				
				<div class="floatLeft movieSidebar">
					{include file=$oView->getTemplateFile('profileMiniView', '/account') oUser=$oMovie->getUser() title='{t}User Profile{/t}' movieID=$oMovie->getID()}
						
					{if $oController->hasAuthority('canSeeUserStats')}
						{include file=$oView->getTemplateFile('profileUserStats', '/account') oUser=$oMovie->getUser() title='{t}User Stats{/t}'}
					{/if}
					
					{assign var=oOtherResults value=$oMovie->getOtherEventMoviesForReview(0, 15, $oUser->getSeed())}
					{if $oOtherResults->getTotalResults() > 0}
						<a href="/videos/doSearch?UserID={$oMovie->getUserID()}&amp;EventID={$oMovie->getSource()->getEventID()}&amp;SourceID=0" title="{t}Show all other movies from this filmmaker in this competition{/t}" class="more-link">
							{t}{$oOtherResults->getTotalResults()} other movies in this competition.{/t}
						</a>
						
						<div id="actions">
							<a class="prev">{t}&laquo; Previous{/t}</a>
							<a class="next floatRight">{t}More &raquo;{/t}</a>
						</div>
						<div class="scrollable">
							<div class="items">
								{foreach $oOtherResults as $oRelMovie}
								<div class="content">
									<p class="alignCentre noMargin"><a href="{adminMovieLink movieID=$oRelMovie->getID()}" title="{t}Go to{/t} {$oRelMovie->getShortDesc()|xmlstring}"><img src="{$oRelMovie->getThumbnailUri('s')}" class="thumb" /></a></p>
									<p class="alignCentre noMargin">{$oRelMovie->getShortDesc()|truncate:25:'..'|xmlstring}</p>
									{if $oUser->isAuthorised('setStatus') && ($oRelMovie->getStatus() == mofilmMovie::STATUS_PENDING || $oUser->getPermissions()->isRoot())}
										<p class="alignRight noMargin"><a href="{$statusURI}/{$oRelMovie->getID()}/Rejected" title="{t}Reject Movie{/t}" class="ajaxUpdate">{t}Reject{/t}</a></p>
									{/if}
								</div>
								{/foreach}
							</div>
							<div class="clearBoth"></div>
						</div>
					{else}
						<p>{t}There are no other videos by this filmmaker in this competition.{/t}</p>
					{/if}
				</div>
			{/if}
			
			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}