{include file=$oView->getTemplateFile('header','/shared') pageTitle="Event Has Closed"}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<h1>{t}File No Longer Available{/t}</h1>
			<p>{t}The event this file is for has closed and is therefore no longer available.{/t}</p>
		</div>
	</div>
		
{include file=$oView->getTemplateFile('footer','/shared')}