{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Login{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<div class="floatLeft main">
				<form id="loginForm" action="{$doLoginUri}" method="post" name="loginForm">
					<h1>{t}用户登录{/t}</h1>
					<div class="formFieldContainer">
						<h3>{t}邮箱地址{/t}</h3>
						<div class="field"><input name="username" type="text" value="" class="required string" /></div>
					</div>
					<div class="formFieldContainer">
						<h3>{t}密码{/t}</h3>
						<div class="field"><input name="password" type="password" class="required string" /></div>
					</div>
					<div>
						<input type="submit" name="submit1" value="{t}登录{/t}" class="submit login" />
						<input type="hidden" name="redirect" value="{$redirect|escape:'url'}" />
						<input type="hidden" name="_sk" value="{$formSessionKey}" />
						<input id="facebookId" type="hidden" name="facebookID" value="" />
					</div>

					<div>
						<ul class="regActions">
							<li><a href="{$forgotcnPasswordUri}">{t}忘记了密码?{/t}</a></li>
							<li><a href="{$activationcnUri}">{t}没收到激活邮件?{/t}</a></li>
						</ul>
					</div>
				</form>
			</div>

			<div class="floatbar">
				<div>
					<a href="{$registercnUri}" id="registerButton"><span>{t}注册帐户{/t}</span></a>
				</div>

				<h3>{t}注册优势{/t}</h3>
				<ul class="regBenefits">
					<li>{t}与MOFILM社区紧密联系{/t}</li>
					<li>{t}独有新闻{/t}</li>
					<li>{t}下载竞赛资源{/t}</li>
				</ul>
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}