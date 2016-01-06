{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Registration Email Sent{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<div class="floatLeft main">
				<form id="registerForm" action="" method="" name="registerForm" class="dropShadow">
					<h2 class="noMargin">{t}Registration Email Sent{/t}</h2>
					<p>
						{t}Thanks for taking the time to register with MOFILM.{/t}
						{t}We've sent you an email to <strong>{$email}</strong> with a link to activate your account.{/t}
						{t}You should receive the email shortly, if not <a href="{$activationUri}">click here to resend the email</a>.{/t}
					</p>
					<p>
						{t}Why not sign up to our <a href="http://www.facebook.com/pages/MOFILM/123362452780" target="_blank">Facebook</a>
						or <a href="http://www.twitter.com/MOFILMugc" target="_blank">Twitter</a> communities while you are here.{/t}
					</p>
					<p>
						{t}<a href="{$loginUri}">Return to login</a>{/t}
					</p>
				</form>
			</div>
			
			<div class="floatLeft sideBar">
				<p class="alignCenter">
					<a href="{$mofilmWwwUri}/competitions/" title="{t}MOFILM: Open Competitions{/t}"><img src="{$themeimages}/competitions-open.jpg" alt="open" /></a>
					&nbsp;&nbsp;
					<a href="{$mofilmWwwUri}/competitions/past" title="{t}MOFILM: Past Competitions{/t}"><img src="{$themeimages}/competitions-past.jpg" alt="past" /></a>
				</p>
			</div>
			
			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}