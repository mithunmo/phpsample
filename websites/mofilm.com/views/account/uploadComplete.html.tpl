{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Upload a Movie{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}
	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			<div class="floatLeft main">
				<div id="registerForm">
					<h2 class="noMargin">{t}Please complete your Profile information{/t}</h2>
					<p>{t}You need to fill Username, FirstName, Surname , Phone number and Skills before you can upload Video{/t}</p>
					<p><a href="/account/profile">{t}Continue to profile{/t}</a></p>
				</div>
			</div>	
		<br class="clearBoth" />	
		</div>				
	</div>	
{include file=$oView->getTemplateFile('footer', 'shared')}