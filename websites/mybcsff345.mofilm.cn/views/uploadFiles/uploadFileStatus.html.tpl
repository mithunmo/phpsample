{include file=$oView->getTemplateFile('header','/shared') pageTitle="File Download"}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<h1>{t}Upload Document{/t}</h1>

			<div class="downloadContainer">
				<h3>{t}{$message}{/t}</h3>
			</div>
		</div>
	</div>
		
{include file=$oView->getTemplateFile('footer','/shared')}