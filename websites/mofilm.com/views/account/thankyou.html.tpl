{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}MOFILM Referral Program{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<h2>{t}Thank you. Your friend has been sent an email.{/t}</h2>
			<a href="/account/reward">{t}Refer more Filmmakers{/t}</a>
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}