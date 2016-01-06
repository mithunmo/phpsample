{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}500 - Internal Server Error{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="sbcontentleft">
		<div class="sbcontent">
			<h2>{t}Internal Server Error{/t}</h2>
			<p>{t}We are sorry, but an unrecoverable internal error was encountered. It has been logged.{/t}</p>
			{include file=$oView->getTemplateFile('debug', '/error')}
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}