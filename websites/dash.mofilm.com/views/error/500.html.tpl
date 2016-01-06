{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}500 - Internal Server Error{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="content">
		{include file=$oView->getTemplateFile('statusMessage', '/shared') nocache}

		<div class="graytitle">{t}Internal Server Error{/t}</div>

		<ul class="pageitem">
			<li class="textbox">
				{t}We are sorry, but an unrecoverable internal error was encountered. It has been logged.{/t}
			</li>
		</ul>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}