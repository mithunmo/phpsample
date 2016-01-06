{include file=$oView->getTemplateFile('header', 'error') pageTitle='{t}Invalid Action{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<div>
				<h2>{t}Invalid Action{/t}</h2>
				<p>{t}The action you requested is not permitted for this request.{/t}</p>
				<p>{t}Please try again using the links and forms on the site.{/t}</p>
				<p>{t}If you continue to see this message, contact Mofilm.{/t}</p>
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'error')}