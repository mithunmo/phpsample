{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Login{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<form id="loginForm" action="{$doLoginUri}" method="post" name="loginForm">
				<div class="floatLeft accountInfo">
					<h2>{t}Login Required{/t}</h2>
					<p>{t}You must login to access this site.{/t}</p>
				</div>

				<div class="floatLeft accountForm">
					<div class="formFieldContainer">
						<h3>{t}Email Address:{/t}</h3>
						<div class="field"><input name="username" type="text" value="" class="required string" /></div>
					</div>
					<div class="formFieldContainer">
						<h3>{t}Password:{/t}</h3>
						<div class="field"><input name="password" type="password" class="required string" /></div>
					</div>
					<br/>
					<div>
						<input type="submit" name="submit" value="Submit" class="submit" />
						<input type="hidden" name="redirect" value="{$redirect|escape:'url'}" />
						<input type="hidden" name="_sk" value="{$formSessionKey}" />
					</div>
					<div><p><a href="{$forgotPasswordUri}">{t}Forgotten your password?{/t}</a></p></div>
				</div>
				
				<br class="clearBoth" />
			</form>
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}