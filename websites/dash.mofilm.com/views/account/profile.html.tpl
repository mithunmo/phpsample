{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Your Profile{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="content">
		{include file=$oView->getTemplateFile('statusMessage', '/shared') nocache}

		<div class="graytitle">{t}Your Profile{/t}</div>

		<div class="pageitem">
			<div class="textbox">
				{t}To make changes to your profile, please login from your computer and not a mobile device.{/t}<br />
				{t}Go to <a href="/home" title="Your Dashboard">your dashboard</a>.{/t}
			</div>
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}