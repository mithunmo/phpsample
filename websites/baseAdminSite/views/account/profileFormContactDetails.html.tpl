<h3><a href="#">{t}Contact Details{/t}</a></h3>
<div>
	<div class="formFieldContainer">
		<h4>{t}Phone Number{/t}</h4>
		<p><input name="Phone" type="text" value='{$oUser->getParamSet()->getParam("Phone")}' class="disabled" /></p>
	</div>

	<div class="formFieldContainer">
		<h4>{t}Mobile Phone Number{/t}</h4>
		<p><input name="MobilePhone" type="text" value='{$oUser->getParamSet()->getParam("MobilePhone")}' class="disabled" /></p>
	</div>
	
	<div class="formFieldContainer">
		<h4>{t}Skype{/t}</h4>
		<p><input name="Skype" type="text" value='{$oUser->getParamSet()->getParam("Skype")}' class="disabled" /></p>
	</div>

	<div class="formFieldContainer">
		<h4>{t}Address{/t}</h4>
		<p><input name="Address1" type="text" value='{$oUser->getParamSet()->getParam("Address1")}' class="disabled string" /></p>
		<p><input name="Address2" type="text" value='{$oUser->getParamSet()->getParam("Address2")}' class="disabled string" /></p>
	</div>

	<div class="formFieldContainer">
		<h4>{t}City{/t}</h4>
		<p><input name="City" type="text" value='{$oUser->getParamSet()->getParam("City")}' class="disabled" /></p>
	</div>

	<div class="formFieldContainer">
		<h4>{t}County{/t}</h4>
		<p><input name="County" type="text" value='{$oUser->getParamSet()->getParam("County")}' class="disabled" /></p>
	</div>

	<div class="formFieldContainer">
		<h4>{t}Postcode{/t}</h4>
		<p><input name="Postcode" type="text" value='{$oUser->getParamSet()->getParam("Postcode")}' class="disabled" /></p>
	</div>

	<div class="formFieldContainer">
		<h4>{t}Country{/t}</h4>
		<p>{territorySelect name='territory' selected=$oUser->getTerritoryID() class="disabled"}</p>
	</div>
</div>