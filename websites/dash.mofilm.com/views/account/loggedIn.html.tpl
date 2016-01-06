{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Logged In{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="content">
		{include file=$oView->getTemplateFile('statusMessage', '/shared') nocache}

		<div class="graytitle">{t}Welcome back {$oUser->getFirstname()}{/t}</div>

		<div class="pageitem">
			<div class="textbox">
				{t}You have successfully logged in to Mofilm.{/t}<br />
				{t}Go to <a href="/home" title="Your Dashboard">your dashboard</a>.{/t}
			</div>
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}