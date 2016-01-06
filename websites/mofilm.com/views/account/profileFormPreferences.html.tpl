<h3><a href="#">{t}Preferences{/t}</a></h3>
<div>
	<div class="formFieldContainer">
		<h4>
			{t}Language{/t}
			<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
		</h4>
		<p>{languageSelect name="Prefs[Language]" useISO=true selected=$oUser->getParamSet()->getParam("Language")|default:'en'}</p>

		{help text="{t}At MOFILM we try to translate our messages and websites, this option allows you to receive messages in an alternative language. If we don't have a translation, you will still receive English.{/t}"}
	</div>

	<div class="formFieldContainer">
		<h4>
			{t}Receive Private Message Alerts{/t}
			<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
		</h4>
		<p>{booleanSelect name="Prefs[PrivateMessageAlerts]" selected=$oUser->getParamSet()->getParam("PrivateMessageAlerts") true='Yes' false='No'}</p>

		{help text="{t}We use private messages to contact you about issues with your uploaded films. By default we send an email to you when you receive a new message. Set this to 'No' to not receive these messages.{/t}"}
	</div>
	<!--div class="formFieldContainer">
		<h4>
			{t}Enable review step before submitting the video to competition{/t}
			<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
		</h4>
		<p>{booleanSelect name="autoCommitStatus" selected=$oUser->getAutoCommitStatus() true='Enable' false='Disable'}</p>		
		
		{help text="{t}This flag shall signify whether the user wishes their submitted videos to be automatically committed to the moderation & judging phase {/t}"}
	</div-->
	
</div>
