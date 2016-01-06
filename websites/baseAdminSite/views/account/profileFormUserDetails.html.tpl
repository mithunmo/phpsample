<h3><a href="#">{$title|default:'{t}About You{/t}'}</a></h3>
<div>
	<div class="formFieldContainer">
		<h4>{t}Firstname{/t}</h4>
		<p><input name="Firstname" type="text" value="{$oUser->getFirstname()}" class="disabled required"/></p>
	</div>

	<div class="formFieldContainer">
		<h4>{t}Surname{/t}</h4>
		<p><input name="Surname" type="text" value="{$oUser->getSurname()}" class="disabled required" /></p>
	</div>
        
        <div class="formFieldContainer">
		<h4>{t}Showreel{/t}</h4>
		<p><input name="ShowReel" type="text" value="{$oUser->getParamSet()->getParam('ShowReelURL')}" class="string" /></p>
	</div>
        
	<div class="formFieldContainer">
		<h4>{t}School Name{/t}</h4>
		<p><input name="SchoolName" type="text" value="{$oUser->getParamSet()->getParam('SchoolName')}" class="string" /></p>
	</div>
	
	<div class="formFieldContainer">
		<h4>{t}Date of Birth{/t}</h4>
		<p>
			{html_select_date start_year='1900' field_order='DMY' prefix='' field_array='DateOfBirth' time=$oUser->getParamSet()->getParam('DateOfBirth') day_value_format='%02d' class="disabled"}
			Age: {$oUser->getParamSet()->getParam('DateOfBirth')|convertDateOfBirthToYears}
		</p>
	</div>
	
	<div class="formFieldContainer">
		<h4>{t}Skills{/t}</h4>
		<p><input name="Skills" type="text" value="{$oUser->getParamSet()->getParam('Skills')}" class="disabled string" /></p>
	</div>
	
	<div class="formFieldContainer">
		<h4>{t}Occupation{/t}</h4>
		<p><input name="Occupation" type="text" value="{$oUser->getParamSet()->getParam('Occupation')}" class="disabled string" /></p>
	</div>

	<div class="formFieldContainer">
		<h4>{t}Company{/t}</h4>
		<p><input name="Company" type="text" value="{$oUser->getParamSet()->getParam('Company')}" class="disabled string" /></p>
	</div>

	<div class="formFieldContainer">
		<h4>{t}Website{/t}</h4>
		<p><input name="Website" type="text" value="{$oUser->getParamSet()->getParam('Website')}" class="disabled string" /></p>
	</div>
	<div class="formFieldContainer">
		<h4>{t}Access to MofilmMaker{/t}</h4>
		<p><input name="mofilmmaker" type="text" value="{if 
			$oUser->getParamSet()->getParam('MM')}
			{$oUser->getParamSet()->getParam('MM')}
			{else}
			0
			{/if}
			" class="disabled string" /></p>
	</div>
	
</div>