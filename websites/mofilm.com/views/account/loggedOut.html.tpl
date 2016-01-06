{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Logged Out{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<div class="floatLeft accountInfo">
				<h2>{t}Logout Successful{/t}</h2>
				<p>{t}You have been successfully logged out. Please wait to be redirected.{/t}</p>
				<p>{t}If you are not re-directed, <a href="{$metaRedirect}">please click to continue</a>.{/t}</p>
			</div>

			<div class="floatLeft accountForm">
				
			</div>
			
			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}