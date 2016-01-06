<mofilm>
	{include file=$oView->getTemplateFile('request', '/shared')}
	<response type="error">
		<message>500: Internal Server Error</message>
	</response>
	{include file=$oView->getTemplateFile('debug','/error')}
</mofilm>