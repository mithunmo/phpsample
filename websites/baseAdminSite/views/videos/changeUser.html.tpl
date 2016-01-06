{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Videos - Change User - {/t}'|cat:$oModel->getMovieID()}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<div class="editTitle">
				<span class="imgWrap"><img src="{$adminEventFolder}/{$oMovie->getSource()->getEvent()->getLogoName()}.jpg" width="50" height="28" border="0" alt="{$oMovie->getSource()->getEvent()->getName()}" title="{t}Event: {/t}{$oMovie->getSource()->getEvent()->getName()}" class="valignMiddle" /></span>
				<span class="imgWrap"><img src="{$adminSourceFolder}/{$oMovie->getSource()->getLogoName()}.jpg" width="50" height="28" border="0" alt="{$oMovie->getSource()->getName()}" title="{t}Source: {/t}{$oMovie->getSource()->getName()}" class="valignMiddle" /></span>
				<h2>{t}Videos{/t} : {$oMovie->getID()} : {$oMovie->getTitle()}</h2>
			</div>
			
			<form id="changeUserSearch" action="{$changeUserURI}/{$oMovie->getID()}" method="get" name="changeUserForm">
				<div class="content">
					<div class="daoAction">
						<a href="{$editURI}/{$oMovie->getID()}" title="{t}Back to Movie{/t}">
							<img src="{$themeicons}/32x32/action-back.png" alt="{t}Back to Movie{/t}" class="icon" />
							{t}Back to Movie{/t}
						</a>
					</div>
					<div class="clearBoth"></div>
				</div>

				<div class="content">
					<div class="formFieldContainer">
						<h4 class="noMargin">{$oMovie->getTitle()|xmlstring}</h4>
						<p class="noMargin">for {$oMovie->getSource()->getEvent()->getName()} - {$oMovie->getSource()->getName()}</p>
					</div>

					<table class="data">
						<thead>
							<tr>
								<th>{t}Active{/t}</th>
								<th>{t}Private{/t}</th>
								<th>{t}Status{/t}</th>
								<th>{t}Duration{/t}</th>
								<th>{t}Production Year{/t}</th>
								<th>{t}Moderated{/t}</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>{$oMovie->getActive()}</td>
								<td>{if $oMovie->getPrivate()}Yes{else}No{/if}</td>
								<td>{$oMovie->getStatus()}</td>
								<td>{$oMovie->getDuration()} seconds</td>
								<td>{$oMovie->getProductionYear()}</td>
								<td>{if $oMovie->getModerated()}{$oMovie->getModerated()|date_format:'%d-%m-%Y'}{else}No{/if}</td>
							</tr>
						</tbody>
					</table>
				</div>
				
				<div class="content">
					<p>{t}Search for the user to assign this movie to using the fields below.{/t}</p>
					<table class="data">
						<thead>
							<tr>
								<th>
									<label for="searchUserID">{t}User ID{/t}</label><br />
									<input id="searchUserID" type="text" name="UserID" value="{$oResults->getSearchInterface()->getUserID()|xmlstring}" class="short" />
								</th>
								<th>
									<label for="searchEmail">{t}Email Address{/t}</label><br />
									<input id="searchEmail" type="text" name="Email" value="{$oResults->getSearchInterface()->getUserEmailAddress()|xmlstring}" />
								</th>
								<th>
									<label for="searchName">{t}Name{/t}</label><br />
									<input id="searchName" type="text" name="Name" value="{$oResults->getSearchInterface()->getKeywords()|xmlstring}" />
								</th>
								<th>{t}Country{/t}</th>
								<th>{t}City{/t}</th>
								<th class="last">
									<button type="submit" name="SearchForUsers" value="Search" title="{t}Search{/t}" class="floatRight">
										<img src="{$themeicons}/32x32/search.png" alt="{t}Search{/t}" class="icon" />
										{t}Search{/t}
									</button>
								</th>
							</tr>
						</thead>
						<tbody>
							{if $oResults->getTotalResults() > 0}
								{assign var=offset value=$oResults->getSearchInterface()->getOffset()|default:0}
								{assign var=limit value=$oResults->getSearchInterface()->getLimit()}
								{assign var=totalObjects value=$oResults->getTotalResults()}

								{foreach $oResults as $oResultUser}
									<tr class="{cycle values=",alt"}">
										<td>{$oResultUser->getID()}</td>
										<td>{$oResultUser->getEmail()}</td>
										<td>{$oResultUser->getFullname()|xmlstring}</td>
										<td>{$oResultUser->getTerritory()->getCountry()}</td>
										<td>{$oResultUser->getParamSet()->getParam('City')}</td>
										<td class="alignRight">
											<a href="{$changeUserURI}/{$oMovie->getID()}/{$oResultUser->getID()}" title="{t}Select this user{/t}">
												<img src="{$themeicons}/32x32/action-next.png" alt="{t}Select this user{/t}" class="icon" />
											</a>
										</td>
									</tr>
								{/foreach}

								{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=4}
							{else}
								<tr colspan="6">{t}No results found for search.{/t}</tr>
							{/if}
						</tbody>
					</table>
				</div>
			</form>
		</div>
	</div>
	
	<script type="text/javascript">
	<!--
	var availableRoles = {$availableRoles};
	//-->
	</script>

{include file=$oView->getTemplateFile('footer', 'shared')}