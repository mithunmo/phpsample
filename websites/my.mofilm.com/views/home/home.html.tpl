{include file=$oView->getTemplateFile('header','/shared') pageTitle="{t}My MOFILM - MOFILMs filmmakers space{/t}"}
{include file=$oView->getTemplateFile('menu','/shared')}
	
	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<h1>{t}my.mofilm.com Coming soon!{/t}</h1>
			<p>{t}Coming soon: Our filmmakers will have their own space to show what they can do.{/t}</p>
			<p>{t}Due Q1 2011 - with updates throughout the year.{/t}</p>
		</div>
	</div>
	
{include file=$oView->getTemplateFile('footer','/shared')}