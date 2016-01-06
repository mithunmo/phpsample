{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Users - Edit{/t} - '|cat:$oObject->getEmail()}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<h2>{t}Users - Edit{/t} - {if strlen($oObject->getFullname()) > 3}{$oObject->getFullname()}{else}{$oObject->getEmail()}{/if}</h2>
			<form id="userDetailsForm" class="monitor" action="{$doEditUri}" method="post" name="profileForm" enctype="multipart/form-data">
				<div class="hidden"><input type="hidden" name="UserID" value="{$oObject->getID()}" /></div>
				<div class="floatLeft accountDetails">
					<div class="content">
						<div class="daoAction">
							<a href="javascript:history.go(-1);" title="{t}Previous Page{/t}">
								<img src="{$themeicons}/32x32/action-back.png" alt="{t}Previous Page{/t}" class="icon" />
								{t}Previous Page{/t}
							</a>
							{if $oController->hasAuthority('usersController.message')}
							<a href="{$messageUri}/{$oObject->getID()}" title="{t}Message User{/t}">
								<img src="{$themeicons}/32x32/action-send.png" alt="{t}Message User{/t}" class="icon" />
								{t}Message User{/t}
							</a>
							{/if}
							<button type="reset" name="Cancel" title="{t}Reset Changes{/t}">
								<img src="{$themeicons}/32x32/action-undo.png" alt="{t}Reset Changes{/t}" class="icon" />
								{t}Reset Changes{/t}
							</button>
							{if $oController->hasAuthority('usersController.doEdit')}
							<button type="submit" name="UpdateProfile" value="Save" title="{t}Save Changes{/t}">
								<img src="{$themeicons}/32x32/action-do-edit-object.png" alt="{t}Save Changes{/t}" class="icon" />
								{t}Save Changes{/t}
							</button>
							{/if}
						</div>
						<div class="clearBoth"></div>
					</div>
				
					<div class="content">
						<div id="userFormAccordion">
							{if $oController->hasAuthority('usersController.canSeeLoginDetails')}
								<h3><a href="#">{t}Login Details{/t}</a></h3>
								<div>
									<div class="formFieldContainer">
										<h4>{t}Email Address{/t}</h4>
										<p><em>{$oObject->getEmail()|xmlstring}</em></p>
									</div>
									
									{if $oController->hasAuthority('usersController.canChangeStatus')}
										<div class="formFieldContainer">
											<h4>{t}Account Enabled{/t}</h4>
											<p>{yesNoSelect name='Enabled' selected=$oObject->getEnabled()}</p>
										</div>
									{/if}
								</div>
							{/if}
							
							{include file=$oView->getTemplateFile('profileFormUserDetails', '/account') title='{t}User Details{/t}' oUser=$oObject}
						
							{include file=$oView->getTemplateFile('profileFormContactDetails', '/account') oUser=$oObject}
							
							{if $oController->hasAuthority('usersController.canChangeStatus')}
								<h3><a href="#">{t}Message History{/t}</a></h3>
								<div>
									<table class="data">
										<thead>
											<tr>
												<th>{t}Sent{/t}</th>
												<th>{t}Subject{/t}</th>
												<th>{t}Type{/t}</th>
											</tr>
										</thead>
										<tbody>
											{foreach $oModel->getMessageHistory() as $oMessage}
											<tr class="{cycle values='alt,'}">
												<td>{$oMessage->getSentDate()|date_format:"%Y-%m-%d %H:%M"|default:'Queued'}</td>
												<td>{$oMessage->getMessageSubject()}</td>
												<td>{$oMessage->getOutboundType()->getDescription()}</td>
											</tr>
											{/foreach}
										</tbody>
									</table>
								</div>
							{/if}
							
							{if $oController->hasAuthority('usersController.canChangeStatus')}
								<h3><a href="#">{t}Private Message History{/t}</a></h3>
								<div>
									<table class="data">
										<thead>
											<tr>
												<th>{t}To{/t}</th>
												<th>{t}Subject{/t}</th>
											</tr>
										</thead>
										<tbody>
											{foreach $oModel->getPrivateMessageHistory() as $oMessage}
											<tr class="{cycle values='alt,'}">
												<td>{$oMessage->getRecipient()->getFullname()}</td>
												<td>{$oMessage->getSubject()}</td>
											</tr>
											{/foreach}
										</tbody>
									</table>
								</div>
							{/if}

							{if $oController->hasAuthority('usersController.canEditProfile')}
								{include file=$oView->getTemplateFile('profileFormMyMofilmSettings', '/account') oUser=$oObject}
							{/if}
						</div>
					</div>
				</div>

				<div class="floatLeft accountStats">
					{include file=$oView->getTemplateFile('profileMiniView', '/account') oUser=$oObject title='{t}User Profile{/t}'}
					
					{include file=$oView->getTemplateFile('profileUserStats', '/account') oUser=$oObject title='{t}User Stats{/t}'}
					
					{include file=$oView->getTemplateFile('profileUserVideos', '/account') oUser=$oObject title='{t}User Videos{/t}' novideos='{t}User has not uploaded any movies.{/t}'}
				</div>
			</form>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}