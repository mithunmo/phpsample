{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Reset Password{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<form action="{$doForgotPasswordUri}" method="post" name="resetPassword">
				<div class="floatLeft accountInfo">
					<h2>{t}忘记密码{/t}</h2>
					<p>{t}请输入您与MOFILM注册的邮箱地址{/t}</p>
				</div>

				<div class="floatLeft accountForm">
					<div class="formFieldContainer">
						<h3>{t}邮箱名:{/t}</h3>
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