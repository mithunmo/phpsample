{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Your Profile{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<form id="profileForm" class="monitor" action="{$doProfileUpdateUri}" method="post" name="profileForm" accept-charset="utf-8">
				<div class="floatLeft accountDetails">
					<h2>{t}Edit Your Profile{/t}</h2>
					<p>{t}Here you can edit your user profile. To change your password, you must enter your existing password.{/t}</p>
					<p>
						{t}To submit a video you must enter your phone number and full postal address so we can contact you if your video is shortlisted.{/t}
					</p>
					
					<div class="content">
						<div class="daoAction">
							<button type="reset" name="Cancel" title="{t}Reset Changes{/t}">
								<img src="{$themeicons}/32x32/action-undo.png" alt="{t}Reset Changes{/t}" class="icon" />
								{t}Reset Changes{/t}
							</button>
							<button type="submit" name="UpdateProfile" value="Save" title="{t}Save{/t}">
								<img src="{$themeicons}/32x32/action-do-edit-object.png" alt="{t}Save{/t}" class="icon" />
								{t}Save Changes{/t}
							</button>
						</div>
						<div class="clearBoth"></div>
					</div>
				
					<div class="content">
						<div id="userFormAccordion">
							{include file=$oView->getTemplateFile('profileFormLoginDetails')}
	
							{include file=$oView->getTemplateFile('profileFormUserDetails')}
						
							{include file=$oView->getTemplateFile('profileFormContactDetails')}
							
							{include file=$oView->getTemplateFile('profileFormPreferences')}
							
							{include file=$oView->getTemplateFile('profileFormFavourites')}

							{if $oUser->getStats()->getTotalApproved() > 0 || $oUser->getPermissions()->isRoot()}
								{*include file=$oView->getTemplateFile('profileFormMyMofilmSettings')*}
							{/if}
						</div>
					</div>
				</div>

				<div class="floatLeft accountStats">
					<p>{t}As a registered user you can download competition briefs, assets, resources and upload your videos.{/t}</p>
					<p>{t}Registered on {$oUser->getRegistered()|date_format:"%d/%m/%y"}{/t}</p>
					
					{include file=$oView->getTemplateFile('profileUserStats')}
					
					{include file=$oView->getTemplateFile('profileUserVideos')}
				</div>
			</form>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}