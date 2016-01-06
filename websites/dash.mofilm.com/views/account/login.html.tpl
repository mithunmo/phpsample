{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Login{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="content">
		{include file=$oView->getTemplateFile('statusMessage', '/shared') nocache}

		<form id="loginForm" action="{$doLoginUri}" method="post" name="loginForm">
			<fieldset>
				<div class="graytitle">{t}Dash Login{/t}</div>
				
				<ul class="pageitem">
					<li class="bigfield"><input placeholder="{t}Username{/t}" type="text" name="username" /></li>
					<li class="bigfield"><input placeholder="{t}Password{/t}" type="password" name="password" /></li>
					<li class="button"><input name="name" type="submit" value="{t}Login{/t}" /></li>
				</ul>

				<input type="hidden" name="redirect" value="{"/home"|escape:'url'}" />
				<input type="hidden" name="_sk" value="{$formSessionKey}" />
			</fieldset>
		</form>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}