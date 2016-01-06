<mofilm>
	{include file=$oView->getTemplateFile('request', '/shared')}
	<response type="result">
		<user>
			<userID>{$oUser->getID()}</userID>
			<firstname>{$oUser->getFirstname()|xmlstring}</firstname>
			<surname>{$oUser->getSurname()|xmlstring}</surname>
			<address1>{$oUser->getParamSet()->getParam('Address1')|xmlstring}</address1>
			<address2>{$oUser->getParamSet()->getParam('Address2')|xmlstring}</address2>
			<city>{$oUser->getParamSet()->getParam('City')|xmlstring}</city>
			<county>{$oUser->getParamSet()->getParam('County')|xmlstring}</county>
			<postcode>{$oUser->getParamSet()->getParam('Postcode')|xmlstring}</postcode>
			<country>{$oUser->getTerritory()->getCountry()|xmlstring}</country>
			<iso2>{$oUser->getTerritory()->getShortName()|xmlstring}</iso2>
		</user>
	</response>
</mofilm>