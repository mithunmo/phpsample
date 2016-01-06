{include file=$oView->getTemplateFile('header','/shared') pageTitle="MOFILM - Home"}
{include file=$oView->getTemplateFile('menu','/shared')}
	
	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<h1>mofilm.com</h1>
			<p>This is mofilm.com's home page.</p>
			<p>You now need to customise the views and build your site.</p>

		</div>
	</div>
	
{include file=$oView->getTemplateFile('footer','/shared')}