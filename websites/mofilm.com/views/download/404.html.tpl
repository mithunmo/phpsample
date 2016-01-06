{include file=$oView->getTemplateFile('header','/shared') pageTitle="File Download"}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<h1>{t}File Not Found{/t}</h1>
			<p>{t}Sorry we could not find the requested download file.{/t}</p>
		</div>
	</div>
		
{include file=$oView->getTemplateFile('footer','/shared')}