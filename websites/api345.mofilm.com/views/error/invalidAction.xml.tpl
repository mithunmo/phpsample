<mofilm>
	{include file=$oView->getTemplateFile('request', '/shared')}
	<response type="error">
		<message>Invalid action requested - actions are CaSe sensitive</message>
	</response>
</mofilm>