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
			
			<form id="changeUserSearch" action="{$doChangeUserURI}/{$oMovie->getID()}" method="post" name="changeUserForm">
				<div class="hidden">
					<input type="hidden" name="UserID" value="{$oSwitchUser->getID()}" />
				</div>
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
					<div class="daoAction">
						<a href="{$changeUserURI}/{$oMovie->getID()}" title="{t}Re-select User{/t}">
							<img src="{$themeicons}/32x32/action-undo.png" alt="{t}Re-select User{/t}" class="icon" />
							{t}Re-select User{/t}
						</a>
						{if $oController->hasAuthority('videosController.doChangeUser')}
						<button type="submit" name="UpdateProfile" value="Save" title="{t}Save{/t}">
							<img src="{$themeicons}/32x32/action-do-edit-object.png" alt="{t}Save{/t}" class="icon" />
							{t}Change User{/t}
						</button>
						{/if}
					</div>
					<div class="clearBoth"></div>
				</div>

				<div class="content">
					<p>{t}You have selected the following user to assign to this movie.{/t}</p>
					<p>{t}Please check the details <strong>carefully</strong> before confirming this selection.{/t}</p>

					<div class="floatLeft spacer" style="width: 40%;">
						<h3><a href="#">{t}User Details{/t}</a></h3>
						<div>
							<div class="formFieldContainer">
								<h4>{t}Email Address{/t}</h4>
								<p><em>{$oSwitchUser->getEmail()|xmlstring}</em></p>
							</div>
							
							<div class="formFieldContainer">
								<h4>{t}Name{/t}</h4>
								<p>{$oSwitchUser->getFullname()}</p>
							</div>
							
							<div class="formFieldContainer">
								<h4>{t}Date of Birth{/t}</h4>
								<p>{$oSwitchUser->getParamSet()->getParam('DateOfBirth')}</p>
							</div>

							<div class="formFieldContainer">
								<h4>{t}Occupation{/t}</h4>
								<p>{$oSwitchUser->getParamSet()->getParam('Occupation')}</p>
							</div>

							<div class="formFieldContainer">
								<h4>{t}Company{/t}</h4>
								<p>{$oSwitchUser->getParamSet()->getParam('Company')}</p>
							</div>
						</div>
					</div>

					<div class="floatLeft spacer" style="width: 40%;">
						<h3><a href="#">{t}Contact Details{/t}</a></h3>
						<div>
							<div class="formFieldContainer">
								<h4>{t}Phone Number{/t}</h4>
								<p>{$oSwitchUser->getParamSet()->getParam("Phone")}</p>
							</div>

							<div class="formFieldContainer">
								<h4>{t}Mobile Phone Number{/t}</h4>
								<p>{$oSwitchUser->getParamSet()->getParam("MobilePhone")}</p>
							</div>

							<div class="formFieldContainer">
								<h4>{t}Skype{/t}</h4>
								<p>{$oSwitchUser->getParamSet()->getParam("Skype")}</p>
							</div>

							<div class="formFieldContainer">
								<h4>{t}Address{/t}</h4>
								<p>{$oSwitchUser->getParamSet()->getParam("Address1")}</p>
								<p>{$oSwitchUser->getParamSet()->getParam("Address2")}</p>
							</div>

							<div class="formFieldContainer">
								<h4>{t}City{/t}</h4>
								<p>{$oSwitchUser->getParamSet()->getParam("City")}</p>
							</div>

							<div class="formFieldContainer">
								<h4>{t}County{/t}</h4>
								<p>{$oSwitchUser->getParamSet()->getParam("County")}</p>
							</div>

							<div class="formFieldContainer">
								<h4>{t}Postcode{/t}</h4>
								<p>{$oSwitchUser->getParamSet()->getParam("Postcode")}</p>
							</div>

							<div class="formFieldContainer">
								<h4>{t}Country{/t}</h4>
								<p>{$oSwitchUser->getTerritory()->getCountry()}</p>
							</div>
						</div>
					</div>

					<div class="clearBoth"></div>
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