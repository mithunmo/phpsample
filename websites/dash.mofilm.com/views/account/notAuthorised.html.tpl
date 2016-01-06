{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Access Denied{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="content">
		{include file=$oView->getTemplateFile('statusMessage', '/shared') nocache}

		<div class="graytitle">{t}Access Denied{/t}</div>

		<ul class="pageitem">
			<li class="textbox">
				{t}Sorry, but you are not authorised to access this resource.{/t}
			</li>
			<li class="textbox">
				{t}Go to <a href="/home" title="Your Dashboard">your dashboard</a>.{/t}
			</li>
		</ul>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}