{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Logout{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<form action="{$doLogoutUri}" method="post" name="loginForm">
				<div class="floatLeft accountInfo">
					<h2>{t}Logout{/t}</h2>
					<p>{t}Are you sure you wish to logout?{/t}</p>
					<div>
						<input type="submit" name="submit" value="{t}Yes{/t}" class="submit" />
					</div>
				</div>

				<br class="clearBoth" />
			</form>
			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}