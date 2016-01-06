{include file=$oView->getTemplateFile('momusicheader','/shared') pageTitle="Reset Password"}
<!-- Content Starts --> 
<div style="height:300px;background-image:url(/themes/momusic/images/page_back_border.gif)">
	<div style="width:740px;float:left; height:inherit;">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<form action="{$doForgotPasswordUri}" method="post" name="resetPassword">
				<div style="padding-left: 100px;">
					<h2>{t}Lost Password{/t}</h2>
					<p>{t}Please enter the email address you registered with MOFILM.{/t}</p>
				</div>

				<div style="padding-left: 100px;">
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
</div> <!-- Content Ends -->
</div></div>
{include file=$oView->getTemplateFile('footer','/shared') pageTitle="momusic"}