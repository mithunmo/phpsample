<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="{if $oProfile->getParamSet()->getParam('ProfileText')|count_characters:true > 150 }{$oProfile->getParamSet()->getParam('ProfileText')}{else} MOFILM User Profile of {$oProfile->getFullname()|xmlstring}{/if}" />
		<meta name="author" content="{$appAuthor}" />
		<meta name="copyright" content="{$appCopyright}" />
		<meta name="google-site-verification" content="F8IKQqxsjr_bm8JkmCEpotXG7FK4kYsDZ2Cxy68-eZ8" />
		<meta name="keywords" content="mofilm, video competition, user profile, {$oProfile->getFullname()|xmlstring}, brand video, film maker" />
		{if $metaRedirect}<meta http-equiv="refresh" content="{$metaTimeout|default:10};url={$metaRedirect}" />{/if}
		<title>MOFILM : Profile of {$oProfile->getFullname()|xmlstring}</title>
		<link rel="stylesheet" type="text/css" href="{$themefolder}/css/global.css?{mofilmConstants::CSS_VERSION}" media="screen" />
		<link rel="stylesheet" type="text/css" href="{$themefolder}/css/my.css?{mofilmConstants::CSS_VERSION}" media="screen" />
		<link rel="stylesheet" type="text/css" href="/libraries/jquery-ui/themes/smoothness/jquery-ui.css" media="screen" />
		<!--[if IE 7]>
		<link rel="stylesheet" type="text/css" href="{$themefolder}/css/ie7.css?{mofilmConstants::CSS_VERSION}" />
		<![endif]-->
		{foreach $oView->getResourcesByType('css') as $oResource}
		    {$oResource->toString()}
		{/foreach}
		<link rel="shortcut icon" href="/favicon.ico" />
	</head>

	{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body" class="whale">
		<div class="container">
			<div id="profileblock">
				<div id="profileleft">
					<div id="profileimg">
						{if $oProfile->getAvatar()->getImageFilename()}
							<img src="{$oProfile->getAvatar()->getImageFilename()}" alt="avatar" border="0" />
						{else}
							<img src="{$themeimages}/profile/avatar.jpg" alt="avatar" border="0" />
						{/if}
					</div>

					<div class="profilescoretitle">{t}Last 12 Months{/t}</div>
					<div class="profilescore">
						<div class="position">{t}Rank:{/t} {$oProfile->getPoints()->getPosition()}</div>
						<div class="total">{t}Score:{/t} {$oProfile->getPoints()->getScore()}</div>
					</div>

					<div class="profilescoretitle">{t}All Time{/t}</div>
					<div class="profilescore">
						<div class="position">{t}Rank:{/t} {$oProfile->getPoints()->getPositionAllTime()}</div>
						<div class="total">{t}Score:{/t} {$oProfile->getPoints()->getHighScore()}</div>
					</div>

					<div id="profiletxt">
						<p>{$oProfile->getParamSet()->getParam('ProfileText')|escape:'htmlall':'UTF-8'|nl2br}</p>
					</div>

					<div id="ci-details">
						<div class="board">
							<div><strong>{t}Won{/t}</strong></div>
							<div class="number">{$oProfile->getAwardSet()->getAwardCountByType(mofilmMovieAward::TYPE_FINALIST)}</div>
							<div class="boardtxt">{t}Awards{/t}</div>
						</div>

						<div class="board">
							<div><strong>{t}Shortlisted{/t}</strong></div>
							<div class="number">{$oProfile->getStats()->getShortlistedCount()}</div>
							<div class="boardtxt">{t}Times{/t}</div>
						</div>

						<div class="board last">
							<div><strong>{t}Entered{/t}</strong></div>
							<div class="number">{$oProfile->getStats()->getCompetitionsEntered()}</div>
							<div class="boardtxt">{t}Competitions{/t}</div>
						</div>
					</div>

					<div id="ci-disclaimer">
						{t}MOFILM is not responsible for the content of external Internet sites.{/t}
					</div>
				</div>
				
				<div id="profileright"  class="back_properties">
					<div id="righthd">
						{*<div class="beta"><img src="{$themeimages}/profile/beta.png" alt="beta" /></div>*}
						<div class="tag">{t}MOFILM Profile:{/t} <span class="name">{$oProfile->getFullname()|xmlstring}</span></div>
					</div>
					<div id="profiledetails">
						<table>
							<tbody>
								{if $oProfile->getParamSet()->getParam('Description')}
									<tr>
										<th>{t}Working On{/t}</th>
										<td>{$oProfile->getParamSet()->getParam('Description')|xmlstring}</td>
									</tr>
								{/if}
								{if $oProfile->getParamSet()->getParam('Website')}
									<tr>
										<th>{t}Website{/t}</th>
										<td><a href="http://{$oProfile->getParamSet()->getParam('Website')|replace:'http://':''|xmlstring}" target="_blank">{$oProfile->getParamSet()->getParam('Website')|xmlstring}</a></td>
									</tr>
								{/if}
								{if $oProfile->getParamSet()->getParam('Skills')}
									<tr>
										<th>{t}Skills{/t}</th>
										<td>{$oProfile->getParamSet()->getParam('Skills')|xmlstring}</td>
									</tr>
								{/if}
								
								<tr>
									<th>{t}Location{/t}</th>
									<td>
										<img src="/themes/shared/flags/{$oProfile->getTerritory()->getShortName()|lower}.png" alt="{$oProfile->getTerritory()->getShortName()}" class="valignMiddle"/>
										{if $oProfile->getParamSet()->getParam('City')}{$oProfile->getParamSet()->getParam('City')|xmlstring},{/if} {$oProfile->getTerritory()->getCountry()|xmlstring} 
									</td>
								</tr>
								{if $oProfile->getContributorRoles()}	
								<tr>
									<th>{t}Role{/t}</th>
									<td>
										{$oProfile->getContributorRoles()|xmlstring}
									</td>
								</tr>
								{/if}	
							</tbody>
						</table>
					</div>

					{if $oProfile->getProfileMovieSet()->getCount() > 0}
						<div id="profilemovies">
							<h3>{t}Chosen Videos by{/t} {$oProfile->getFullname()}</h3>
							{foreach $oProfile->getProfileMovieSet() as $oProfileMovie}
								<div class="movie {cycle values='odd,even'}">
									{if $oProfileMovie->getMovie()->isPrivate()}
										<img src="{$oProfileMovie->getMovie()->getThumbnailUri('s')}" alt="Thumbnail" width="90" class="valignMiddle" />
									{else}
										<a href="{$oProfileMovie->getMovie()->getShortUri(0, true)}" title="{$oProfileMovie->getTitle()|xmlstring}"><img src="{$oProfileMovie->getMovie()->getThumbnailUri('s')}" alt="thumbnail" width="90" class="valignMiddle" /></a>
									{/if}
								</div>
							{/foreach}
							<div class="clearBoth"></div>
						</div>
					{/if}

					{if $oProfile->getAwardSet()->getCount() > 0}
						<div id="profileawards">
							<h3>{t}Award Winning Videos by{/t} {$oProfile->getFullname()}</h3>
							<div>
								<table cellpadding="0" cellspacing="0">
									<tbody>
										{foreach $oProfile->getAwardSet()->getBestAwards() as $oAward}
											<tr class="{cycle values="odd,even"}">
												<td class="thumbnail">
													{if $oAward->getMovie()->isPrivate()}
														<img src="{$oAward->getMovie()->getThumbnailUri('s')}" alt="Thumbnail" width="90" class="valignMiddle" />
													{else}
														<a href="{$oAward->getMovie()->getShortUri(0, true)}"><img src="{$oAward->getMovie()->getThumbnailUri('s')}" alt="Thumbnail" width="90" class="valignMiddle" /></a>
													{/if}
												</td>
												<td class="award">
													{if $oAward->getEventPosition()}
														<div class="badge pos{$oAward->getPosition()|xmlstring}"><span class="position">{$oAward->getPosition()|xmlstring}</span></div>
													{/if}
													{if $oAward->isWinner()}
														<div class="badge winner" title="{t}Overall Event Winner{/t}"></div>
													{/if}
													{if $oAward->isShortlisted()}
														<div class="badge shortlisted" title="{t}Shortlisted{/t}"></div>
													{/if}
												</td>
													<td class="title"><strong>{$oAward->getMovie()->getShortDesc()|xmlstring}</strong></td>
												<td class="logos">
													<img title="{$oAward->getAwardTitle()|xmlstring}" src="/resources/client/sources/{$oAward->getSourceID()}.jpg" alt="{$oAward->getEvent()->getName()|xmlstring}" width="80" class="valignMiddle" />
													<img title="{$oAward->getAwardTitle()|xmlstring}" src="/resources/client/events/{$oAward->getEventID()}.jpg" alt="{$oAward->getEvent()->getName()|xmlstring}" width="80" class="valignMiddle" />
												</td>
											</tr>
										{/foreach}
									</tbody>
								</table>
							</div>
						</div>
					{/if}
				</div>
				{*<div class="clearBoth"></div>*}

				{if $oProfile->getCreditAwardSet()->getCount() > 0}
				<div id="profileright"  class="back_properties">
					<div id="profileawards">
						<h3>{t}Award Winning Videos where {/t} {$oProfile->getFullname()} has contribued</h3>
						<div>
							<table cellpadding="0" cellspacing="0">
								<tbody>
									{foreach $oProfile->getCreditAwardSet()->getBestAwards() as $oAward}
										<tr class="{cycle values="odd,even"}">
											<td class="thumbnail">
												{if $oAward->getMovie()->isPrivate()}
													<img src="{$oAward->getMovie()->getThumbnailUri('s')}" alt="Thumbnail" width="90" class="valignMiddle" />
												{else}
													<a href="{$oAward->getMovie()->getShortUri(0, true)}"><img src="{$oAward->getMovie()->getThumbnailUri('s')}" alt="Thumbnail" width="90" class="valignMiddle" /></a>
												{/if}
											</td>
											<td class="award">
												{if $oAward->getEventPosition()}
													<div class="badge pos{$oAward->getPosition()|xmlstring}"><span class="position">{$oAward->getPosition()|xmlstring}</span></div>
												{/if}
												{if $oAward->isWinner()}
													<div class="badge winner" title="{t}Overall Event Winner{/t}"></div>
												{/if}
												{if $oAward->isShortlisted()}
													<div class="badge shortlisted" title="{t}Shortlisted{/t}"></div>
												{/if}
											</td>
											<td class="title">
												<span><strong>{$oAward->getMovie()->getShortDesc()|xmlstring}</strong></span><br />
												<span style="font-size: 10px;"><strong>Role:</strong> {$oAward->getMovie()->getContributorRole({$oProfile->getEmail()})}</span>
											</td>
											<td class="logos">
												<img title="{$oAward->getAwardTitle()|xmlstring}" src="/resources/client/sources/{$oAward->getSourceID()}.jpg" alt="{$oAward->getEvent()->getName()|xmlstring}" width="80" class="valignMiddle" />
												<img title="{$oAward->getAwardTitle()|xmlstring}" src="/resources/client/events/{$oAward->getEventID()}.jpg" alt="{$oAward->getEvent()->getName()|xmlstring}" width="80" class="valignMiddle" />
											</td>
										</tr>
									{/foreach}
								</tbody>
							</table>
						</div>
					</div>
				</div>
				{/if}
				<div class="clearBoth"></div>
			</div>
		</div>
	</div>
		
{include file=$oView->getTemplateFile('footer', 'shared') footerClass='whale'}