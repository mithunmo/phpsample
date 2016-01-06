<h3><a href="#">{t}Contact Details{/t}</a></h3>
<div>
	<div class="formFieldContainer">
		<h4>
			{t}Phone Number{/t}
			<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
		</h4>
		<p><input name="Phone" type="text" value="{$oUser->getParamSet()->getParam("Phone")}"/></p>

		{help text="{t}We need your phone number in case you are short-listed or win a competition. In fact you cannot enter competitions without this.{/t}"}
	</div>
	
	<div class="formFieldContainer">
		<h4>
			{t}Mobile Phone Number{/t}
			<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
		</h4>
		<p><input name="MobilePhone" type="text" value="{$oUser->getParamSet()->getParam("MobilePhone")}" /></p>

		{help text="{t}You don't have to give your mobile number, but it helps us if we need to contact you.{/t}"}
	</div>

	<div class="formFieldContainer">
		<h4>{t}Skype{/t}</h4>
		<p><input name="Skype" type="text" value="{$oUser->getParamSet()->getParam("Skype")}" /></p>
	</div>

	<div class="formFieldContainer">
		<h4>
			{t}Address{/t}
			<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
		</h4>
		<p><input name="Address1" type="text" value="{$oUser->getParamSet()->getParam("Address1")}" class="string" /></p>
		<p><input name="Address2" type="text" value="{$oUser->getParamSet()->getParam("Address2")}" class="string" /></p>

		{help text="{t}Some of our competitions are limited to specific countries, or require us to send you additional information. We need a current address where we can send you important documents.{/t}"}
	</div>

	<div class="formFieldContainer">
		<h4>{t}City{/t}</h4>
		<p><input name="City" type="text" value="{$oUser->getParamSet()->getParam("City")}" /></p>
	</div>

	<div class="formFieldContainer">
		<h4>
			{t}County / State{/t}
			<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
		</h4>
		<p class="stateTextbox"><input id="County" name="County" type="text" value="{$oUser->getParamSet()->getParam("County")|default:''}" class="string" /></p>

		{help text="{t}For people in the USA and Canada with javascript enabled there is auto-complete of the Provinces and States.{/t}"}
	</div>

	<div class="formFieldContainer">
		<h4>{t}Postcode / ZIP{/t}</h4>
		<p><input name="Postcode" type="text" value="{$oUser->getParamSet()->getParam("Postcode")}" /></p>
	</div>

	<div class="formFieldContainer">
		<h4>
			{t}Country{/t}
			<img src="{$themeicons}/16x16/help.png" alt="{t}help{/t}" class="smallIcon floatRight helpToggle" />
		</h4>
		<p>{territorySelect name='territory' id="territoryID" selected=$oUser->getTerritoryID()}</p>

		{help text="{t}We need to know where you are from because sometimes our competitions are only open to certain countries. It also helps us send important documents to the right place!{/t}"}
	</div>
</div>