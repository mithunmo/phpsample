<h3><a href="#">{t}My.MOFILM.com Profile Settings{/t}</a></h3>
<div>
	{if $oUser->getAvatar()->getImageFilename()}
		<img src="{$oUser->getAvatar()->getImageFilename()}?{$smarty.now}" width="150" height="150" border="0" alt="Profile Image" class="floatLeft spacer profileImage" />
	{/if}
	<p>
		{t}Here you can control your my.mofilm.com profile, including any active information!{/t}
		{t}You can block access to your profile at any time by marking it unavailable.{/t}
		{t}You won't lose your profile name and this can be re-activated at any time.{/t}
	</p>
	<p>
		{t}<strong>Important:</strong> you can change your profile name at any time BUT you cannot revert to a previous name.{/t}
	</p>
	<div class="clearBoth"></div>
	<div class="formFieldContainer">
		<h4>{t}Your Profile Name (must be unique and will be checked).{/t}</h4>
		<p><input id="profileName" name="ProfileName" type="text" value="{$oUser->getProfile()->getProfileName()|xmlstring}" class="long" /></p>
	</div>
	<div class="formFieldContainer">
		<h4>{t}Upload a profile image.{/t} ({t}Max 1MB, will be resized to 200x200px{/t})</h4>
		<p><input id="profileImage" name="Files" type="file" class="long" /></p>
	</div>
	<div class="formFieldContainer">
		<h4>{t}Make your profile available?{/t}</h4>
		<p>{booleanSelect selected=$oUser->getProfile()->getActive() name='ProfileActive' true='Yes' false='No'}</p>
	</div>
	<div class="formFieldContainer">
		<h4>{t}Tell us about you{/t}</h4>
		<p><textarea rows="5" cols="75" name="ProfileText">{$oUser->getParamSet()->getParam('ProfileText')|xmlstring}</textarea></p>
	</div>
</div>