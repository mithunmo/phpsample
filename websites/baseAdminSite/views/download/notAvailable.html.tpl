{include file=$oView->getTemplateFile('header','/shared') pageTitle="File Not Available"}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<h1>{t}File Not Available{/t}</h1>
			<p>{t}The requested file is currently not available.{/t}</p>
		</div>
	</div>
		
{include file=$oView->getTemplateFile('footer','/shared')}