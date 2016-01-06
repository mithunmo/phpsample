<h3><a href="#">{t}Login Details{/t}</a></h3>
<div>
	<div class="formFieldContainer">
		<h4>{t}Email Address{/t}</h4>
		<p><em>{$oUser->getEmail()}</em></p>
	</div>

	<div class="formFieldContainer">
		<h4>{t}Current Password{/t}</h4>
		<p><input name="curPassword" type="password" class="disabled" id="curPassword" /></p>
	</div>

	<div class="formFieldContainer">
		<h4>{t}New Password (leave blank to keep existing password) - min 8 chars{/t}</h4>
		<p><input name="Password" type="password" class="disabled" id="newPassword" /></p>
	</div>

	<div class="formFieldContainer">
		<h4>{t}Confirm Password{/t}</h4>
		<p><input name="confirmPassword" type="password" class="disabled" id="confirmPassword" /></p>
	</div>
</div>