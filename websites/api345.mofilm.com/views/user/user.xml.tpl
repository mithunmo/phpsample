<mofilm>
	{include file=$oView->getTemplateFile('request', '/shared')}
	<response type="result">
		<userID>{$userID}</userID>
		<token>{$token}</token>
	</response>
</mofilm>