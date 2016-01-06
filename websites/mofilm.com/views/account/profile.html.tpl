{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Your Profile{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<form id="profileForm" class="monitor" action="{$doProfileUpdateUri}" method="post" name="profileForm" accept-charset="utf-8">
				<div class="floatLeft accountDetails">
					<div id="profileImageContainer" class="floatLeft spacer">
						{if $oUser->getAvatar()->getImageFilename()}
							<img src="{$oUser->getAvatar()->getImageFilename()}" width="150" height="150" border="0" alt="Profile Image" title="{t}Click to upload a new profile image{/t}" class="profileImage" />
						{/if}
						<script type="text/javascript">
							document.write('<div id="upload" class="alignCenter {if $oUser->getAvatar()->getImageFilename()}hidden{/if}">');
							document.write('<p>{t}Upload a profile image{/t}</p>');
							document.write('<p class="noMargin"><input type="image" id="FileUpload" name="FileUpload" /></p>');
							document.write('<p class="noMargin"><a class="pointer cancel">{t}Cancel{/t}</a></p>');
							document.write('</div>');
						</script>
						<noscript>
							<p class="alignCenter {if $oUser->getAvatar()->getImageFilename()}hidden{/if}">{t}Oops, you must have javascript and Flash to upload images.{/t}</p>
						</noscript>
					</div>
					
					<h2>
						{t}Update Your Profile{/t}
					</h2>

					{$oView->getControllerView('pm', '/account', 'messageCheck')}

					<p>
						{t}Here you can edit your user profile.{/t}
						{t}Click this icon{/t}<img src="{$themeicons}/16x16/help.png" alt="help" class="smallIcon" />{t}to get additional help.{/t}
					</p>
					<p>
						{t}You can upload a profile image and we will resize it to 200x200px.{/t}
						{t}Smaller images will be centred on a white background.{/t}
						{t}You can upload GIF, JPG and PNG files up to 1MB in size.{/t}
					</p>
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
							{include file=$oView->getTemplateFile('profileFormUserDetails', '/account')}
						
							{include file=$oView->getTemplateFile('profileFormContactDetails', '/account')}
							
							{include file=$oView->getTemplateFile('profileFormPreferences', '/account')}

							{include file=$oView->getTemplateFile('profileFormMyMofilmSettings', '/account')}

							{include file=$oView->getTemplateFile('profileFormLoginDetails', '/account')}

							{include file=$oView->getTemplateFile('profileFormFacebookLogin','/account')}
						</div>
					</div>
				</div>

				<div class="floatLeft accountStats">
					<p>{t}As a registered user you can download competition briefs, assets, resources and upload your videos.{/t}</p>
					<p>{t}Registered on {$oUser->getRegistered()|date_format:"%d/%m/%y"}{/t}</p>
					
					{include file=$oView->getTemplateFile('profileUserStats', '/account')}
					
					{include file=$oView->getTemplateFile('profileUserVideos', '/account')}
				</div>
			</form>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}