{include file=$oView->getTemplateFile('header', 'error') pageTitle='{t}Offline{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<div>
				<h2>{t}Offline{/t}</h2>
				<p>{t}The site is currently offline for routine maintenance.{/t}</p>
				<p>{t}We'll be back soon! Please bare with us.{/t}</p>
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'error')}