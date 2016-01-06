{include file=$oView->getTemplateFile('momusicheader','/shared') pageTitle="Registration Email sent"}
<!-- Content Starts --> 
<div style="height:300px;background-image:url(/themes/momusic/images/page_back_border.gif)">
	<div style="width:740px;float:left; height:inherit;">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<div style="padding-left: 100px;">
				<form action="" method="" name="registerForm" class="dropShadow">
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
			
			
			<br class="clearBoth" />
	</div>
</div>
</div> <!-- Content Ends -->
</div></div>
{include file=$oView->getTemplateFile('footer','/shared') pageTitle="momusic"}
