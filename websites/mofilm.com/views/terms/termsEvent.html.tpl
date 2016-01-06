{include file=$oView->getTemplateFile('header','/shared') pageTitle=$oEvent->getName()|cat:' - Terms'}
{include file=$oView->getTemplateFile('menu','/shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<h1>{$oEvent->getName()|xmlstring} Terms</h1>
			{$oEvent|printr}
			{$oEvent->getTerms()|printr}

		</div>
	</div>

{include file=$oView->getTemplateFile('footer','/shared')}