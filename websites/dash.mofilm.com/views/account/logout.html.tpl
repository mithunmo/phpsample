{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Logout{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="content">
		{include file=$oView->getTemplateFile('statusMessage', '/shared') nocache}

		<form action="{$doLogoutUri}" method="post" name="loginForm">
			<fieldset>
				<div class="graytitle">{t}Logout{/t}</div>
				<ul class="pageitem">
					<li class="textbox">{t}Are you sure you wish to logout?{/t}</li>
					<li class="button"><input type="submit" name="submit" value="{t}Yes{/t}" class="submit" /></li>
				</ul>
			</fieldset>
		</form>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}