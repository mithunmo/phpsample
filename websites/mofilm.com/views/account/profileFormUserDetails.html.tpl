<h3><a href="#">{$title|default:'{t}About You{/t}'}</a></h3>
<div>

	<div class="formFieldContainer">
		<h4>
			{t}Username{/t}
			<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
		</h4>
		<p><input id="profileName" name="ProfileName" type="text" value="{$oUser->getProfile()->getProfileName()|xmlstring}" class="long required" /></p>
		Link to Profile : <a href="{$mofilmMyUri}/user/{$oUser->getProfile()->getProfileName()|xmlstring}" class="profileLink" target="_blank">{$mofilmMyUri}/user/<div id="profileNameLive" style="display:inline">{$oUser->getProfile()->getProfileName()|xmlstring}</div></a>
		{help text="{t}You can only use: A-Z, a-z, 0-9, - (hyphen / dash) and _ (underscore). Max length: 50 characters. It is recommended to use lowercase characters and numbers, separate words with a - or _.{/t}"}
	</div>

	<div class="formFieldContainer">
		<h4>{t}Firstname{/t}</h4>
		<p><input name="Firstname" type="text" value="{$oUser->getFirstname()}" class="long required"/></p>
	</div>

	<div class="formFieldContainer">
		<h4>{t}Surname{/t}</h4>
		<p><input name="Surname" type="text" value="{$oUser->getSurname()}" class="long required" /></p>
	</div>

	<div class="formFieldContainer">
		<h4>{t}School Name{/t}</h4>
		<p><input name="SchoolName" type="text" value="{$oUser->getParamSet()->getParam('SchoolName')}" class="string long" /></p>
	</div>

	<div class="formFieldContainer">
		<h4>
			{t}Date of Birth{/t}
			<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
		</h4>
		<p>
			{html_select_date start_year='1900' field_order='DMY' prefix='' field_array='DateOfBirth' time=$oUser->getParamSet()->getParam('DateOfBirth') day_value_format='%02d' class="disabled"}
		</p>
		{help text="{t}We need your date of birth because MOFILM competitions are only open to people aged 16 or over.{/t}"}
	</div>
	<div class="formFieldContainer">
		<h4>
			{t}Skills (Select upto 4 skills){/t}
			<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
		</h4>
		<p> 
		{foreach $roles as $oRole}
			
			{if $oModel->getSkillData($oRole->getDescription())}
			<input type="checkbox" class="skillsp" checked="checked" name="Skills[]" value="{$oRole->getDescription()}"> {$trs->__($oRole->getDescription())} <br/>
			{else}	
			<input type="checkbox" class="skillsp" name="Skills[]" value="{$oRole->getDescription()}"> {$trs->__($oRole->getDescription())}<br/>
			{/if}
		{/foreach}
		</p>
		
		{help text="{t}Select upto 4 skills{/t}"}
	</div>	
	
	<div class="formFieldContainer">
		<h4>
			{t}What are you doing?{/t}
			<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
		</h4>
		<p><input name="Description" type="text" value="{$oUser->getParamSet()->getParam('Description')}" class="long" /></p>

		{help text="{t}Here you can let your fellow filmmakers know what you are working on. Keep it clean and short!{/t}"}
	</div>

	<div class="formFieldContainer">
		<h4>
			{t}Occupation{/t}
			<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
		</h4>
		<p><input name="Occupation" type="text" value="{$oUser->getParamSet()->getParam('Occupation')}" class="string" /></p>

		{help text="{t}What is your day job? Student? Filmmaker? Designer? Let us know, and anyone visiting your profile.{/t}"}
	</div>

	<div class="formFieldContainer">
		<h4>
			{t}Company{/t}
			<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
		</h4>
		<p><input name="Company" type="text" value="{$oUser->getParamSet()->getParam('Company')}" class="string" /></p>

		{help text="{t}Whether you have your own company or work for someone else, tell your fellow MOFILMers{/t}"}
	</div>

	<div class="formFieldContainer">
		<h4>
			{t}Website{/t}
			<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
		</h4>
		<p><input name="Website" type="text" value="{$oUser->getParamSet()->getParam('Website')}" class="string" /></p>

		{help text="{t}Have your own website somewhere else? Why not share that with the rest of MOFILM.{/t}"}
	</div>
	<div class="formFieldContainer">
		<h4>
			{t}Showreel URL - please provide a link to examples of your previous work{/t}
			<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
		</h4>
		<p><input name="ShowReelURL" type="text" id="ShowReelURL"  value="{$oUser->getParamSet()->getParam('ShowReelURL')}" class="string" /></p>

		{help text="{t}Have your videos somewhere else? Why not share that with the rest of MOFILM.{/t}"}
	</div>
</div>