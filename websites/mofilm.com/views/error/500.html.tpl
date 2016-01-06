{include file=$oView->getTemplateFile('header', 'error') pageTitle='{t}500 - Internal Server Error{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<div>
				<h2>{t}Internal Server Error{/t}</h2>
				<p>{t}We are sorry, but an unrecoverable internal error was encountered. It has been logged.{/t}</p>
				{include file=$oView->getTemplateFile('debug', '/error')}
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'error')}