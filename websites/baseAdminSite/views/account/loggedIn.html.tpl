{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Logged In{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<div class="floatLeft accountInfo">
				<h2>{t}Welcome back {$oUser->getFirstname()}{/t}</h2>
				<p>{t}You have successfully logged in to Mofilm.{/t}</p>
				<p>{t}Go to <a href="/home" title="Your Dashboard">your dashboard</a>.{/t}</p>
			</div>
			
			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}