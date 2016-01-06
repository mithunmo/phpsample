{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Offline{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="content">
		{include file=$oView->getTemplateFile('statusMessage', '/shared') nocache}

		<div class="graytitle">{t}Offline{/t}</div>

		<ul class="pageitem">
			<li class="textbox">
				{t}The site is currently offline for routine maintenance.{/t}<br />
				{t}We'll be back soon! Please bare with us.{/t}
			</li>
		</ul>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}