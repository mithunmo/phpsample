{include file=$oView->getTemplateFile('header', 'error') pageTitle='{t}404 - Request Not Found{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<div>
				<h2>{t}Request Not Found{/t}</h2>
				<p>{t}The requested resource could not be located.{/t}</p>
				<p>{t}If you continue to see this message, we might have a bad link.{/t}</p>
				<p>{t}Please let Mofilm know!{/t}</p>
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'error')}