{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Resend Activation Email{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<form action="{$doActivationUri}" method="post" name="activation">
				<div class="floatLeft accountInfo">
					<h2>{t}Resend Activation Email{/t}</h2>
					<p>{t}Please enter the email address you registered with MOFILM.{/t}</p>
				</div>

				<div class="floatLeft accountForm">
					<div class="formFieldContainer">
						<h3>{t}Email Address:{/t}</h3>
						<div class="field"><input name="username" type="text" value="" class="string" /></div>
					</div>
					<br/>
					<div><input type="submit" name="submit" value="{t}Submit{/t}" class="submit" /></div>
				</div>

				<br class="clearBoth" />
			</form>
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}