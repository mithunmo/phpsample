{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Invalid Action{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="content">
		{include file=$oView->getTemplateFile('statusMessage', '/shared') nocache}

		<div class="graytitle">{t}Invalid Action{/t}</div>

		<ul class="pageitem">
			<li class="textbox">
				{t}The action you requested is not permitted for this request.{/t}<br />
				{t}Please try again using the links and forms on the site.{/t}<br />
				{t}If you continue to see this message, contact Mofilm.{/t}
			</li>
			<li class="textbox">
				{t}Go to <a href="/home" title="Your Dashboard">your dashboard</a>.{/t}
			</li>
		</ul>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}