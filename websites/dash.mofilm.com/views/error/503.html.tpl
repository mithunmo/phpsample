{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}503 - Server Not Available{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="content">
		{include file=$oView->getTemplateFile('statusMessage', '/shared') nocache}

		<div class="graytitle">{t}Server Not Available{/t}</div>

		<ul class="pageitem">
			<li class="textbox">
				{t}The server is temporarily not available.{/t}
			</li>
		</ul>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}