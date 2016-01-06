{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}404 - Request Not Found{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="content">
		{include file=$oView->getTemplateFile('statusMessage', '/shared') nocache}

		<div class="graytitle">{t}Request Not Found{/t}</div>

		<ul class="pageitem">
			<li class="textbox">
				{t}The requested resource could not be located.{/t}<br />
				{t}If you continue to see this message, we might have a bad link.{/t}<br />
				{t}Please let Mofilm know!{/t}
			</li>
		</ul>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}