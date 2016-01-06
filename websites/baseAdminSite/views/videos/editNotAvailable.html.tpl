{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Videos - Edit - {/t}'|cat:$oModel->getMovieID()}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<div class="editTitle">
				<img src="{$adminEventFolder}/{$oMovie->getSource()->getEvent()->getLogoName()}.jpg" width="50" height="28" alt="{$oMovie->getSource()->getEvent()->getName()}" title="{t}Event: {/t}{$oMovie->getSource()->getEvent()->getName()}" class="valignMiddle" />
				<img src="{$adminSourceFolder}/{$oMovie->getSource()->getLogoName()}.jpg" width="50" height="28" alt="{$oMovie->getSource()->getName()}" title="{t}Source: {/t}{$oMovie->getSource()->getName()}" class="valignMiddle" />
				<h2>{t}Videos{/t} : {$oMovie->getID()} : {$oMovie->getTitle()}</h2>
			</div>
			
			<form id="movieDetailsForm" class="monitor" action="{$doEditURI}" method="post" name="profileForm">
				<div class="hidden">
					<input type="hidden" name="MovieID" value="{$oMovie->getID()}" />
				</div>
				<div class="floatLeft movieDetails">
					<div class="content">
						<div class="daoAction">
							<a href="javascript:history.go(-1);" title="{t}Previous Page{/t}">
								<img src="{$themeicons}/32x32/action-back.png" alt="{t}Previous Page{/t}" class="icon" />
								{t}Previous Page{/t}
							</a>
							{if $oController->hasAuthority('usersController.message')}
							<a href="/users/message/{$oMovie->getUserID()}?MovieID={$oMovie->getID()}" title="{t}Message User{/t}">
								<img src="{$themeicons}/32x32/action-send.png" alt="{t}Message User{/t}" class="icon" />
								{t}Message User{/t}
							</a>
							{/if}
							{if $oUser->getPermissions()->isRoot()}
							<button type="submit" name="UpdateProfile" value="Save" title="{t}Save{/t}">
								<img src="{$themeicons}/32x32/action-do-edit-object.png" alt="{t}Save{/t}" class="icon" />
								{t}Save Changes{/t}
							</button>
							{/if}
						</div>
						<div class="clearBoth"></div>
					</div>
				
					<div class="content">
						{if !$oUser->getPermissions()->isRoot()}
						<div class="noEditDialogue">
							<p class="status{$oMovie->getStatus()|replace:' ':''}">
								{if $oMovie->getStatus() == mofilmMovieBase::STATUS_ENCODING}
									{t}This movie is currently Encoding and cannot be edited.{/t}
								{else}
									{t}This movie has Failed Encoding and cannot be edited.{/t}
								{/if}
							</p>
						</div>
						{/if}
						
						{if $oUser->getPermissions()->isRoot()}
						<div id="userFormAccordion">
							{include file=$oView->getTemplateFile('editMovieDetails')}
							
							{include file=$oView->getTemplateFile('editMovieComments')}
						</div>
						{/if}
					</div>
				</div>

				<div class="floatLeft movieSidebar">
					{include file=$oView->getTemplateFile('addToFavourites','videos') textLabels=true}
				
					{include file=$oView->getTemplateFile('profileMiniView', '/account') oUser=$oMovie->getUser() title='{t}User Profile{/t}' movieID=$oMovie->getID()}
					
					{if $oController->hasAuthority('canSeeUserStats')}
						{include file=$oView->getTemplateFile('profileUserStats', '/account') oUser=$oMovie->getUser() title='{t}User Stats{/t}'}
					{/if}
					
					{include file=$oView->getTemplateFile('editMovieOtherMovies')}
				</div>
			</form>
			
			<br class="clearBoth" />
		</div>
	</div>
	
	<script type="text/javascript">
	<!--
	var availableRoles = {$availableRoles};
	//-->
	</script>

{include file=$oView->getTemplateFile('footer', 'shared')}