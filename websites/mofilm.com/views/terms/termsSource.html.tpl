{include file=$oView->getTemplateFile('header','/shared') pageTitle=$oSource->getName()|cat:' at '|cat:$oSource->getEvent()->getName()|cat:' - Terms'}
{include file=$oView->getTemplateFile('menu','/shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<h1>{$oSource->getName()|xmlstring} at {$oSource->getEvent()->getName()|xmlstring} Terms</h1>
			{$oSource|printr}
			{$oSource->getTerms()|printr}

		</div>
	</div>

{include file=$oView->getTemplateFile('footer','/shared')}