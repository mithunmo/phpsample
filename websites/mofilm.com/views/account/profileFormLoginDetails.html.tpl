<h3><a href="#">{t}Login Details{/t}</a></h3>
<div>
	<div class="formFieldContainer">
		<h4>
			{t}Email Address{/t}
			<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
		</h4>
		<p><em>{$oUser->getEmail()}</em></p>

		{help text="Your email address is used as your username when you login to any of the MOFILM sites."}
	</div>
	
	{if $oUser->hasPassword()}
		<div class="formFieldContainer">
			<h4>
				{t}Current Password{/t}
				<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
			</h4>
			<p><input name="curPassword" type="password" class="" id="curPassword" /></p>

			{help text="To change your password you must enter your current password."}
		</div>
	{/if}

	<div class="formFieldContainer">
		<h4>{t}New Password (leave blank to keep existing password) - min 8 chars{/t}</h4>
		<p><input name="Password" type="password" class="" id="newPassword" /></p>
	</div>

	<div class="formFieldContainer">
		<h4>{t}Confirm Password{/t}</h4>
		<p><input name="confirmPassword" type="password" class="" id="confirmPassword" /></p>
	</div>
</div>