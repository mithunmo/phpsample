{include file=$oView->getTemplateFile('momusicheader','/shared') pageTitle="momusic"}
<!-- Content Starts --> 
<div style="height:650px;background-image:url(/themes/momusic/images/page_back_border.gif)">
	<div style="width:1030px;float:left;">

		{include file=$oView->getTemplateFile('statusMessage', '/shared')}
		<div>
			<form id="loginForm" action="{$doLoginUri}" method="post" name="loginForm">
				<h1>{t}MOFILM User Login{/t}</h1>
				<div class="formFieldContainer">
					<h3>{t}Email Address:{/t}</h3>
					<div class="field"><input name="username" type="text" value="" class="required string" /></div>
				</div>
				<div class="formFieldContainer">
					<h3>{t}Password:{/t}</h3>
					<div class="field"><input name="password" type="password" class="required string" /></div>
				</div>
				<div>
					<input type="submit" name="submit1" value="{t}Login{/t}" class="submit login" />
					<input type="hidden" name="redirect" value="{$redirect|escape:'url'}" />
					<input type="hidden" name="_sk" value="{$formSessionKey}" />
					<input id="facebookId" type="hidden" name="facebookID" value="" />
					<input id="accessToken" type="hidden" name="accessToken" value="" />
				</div>

				<div id="fb-root" style="padding: 15px 0px 0px 0px;"></div>

				<div id="fb_link_display" style="display: none">
					<div class="loginFbLink"><a class="fb_button fb_button_medium"><span class="fb_button_text">Login with Facebook</span></a></div>
				</div>

				<div id="fb_button_display" style="display: none">	    
					<fb:login-button scope="email,user_birthday,publish_stream">Login with Facebook</fb:login-button>
				</div>



				<div>
					<ul class="regActions">
						<li><a href="{$forgotPasswordUri}">{t}Forgotten your password?{/t}</a></li>
						<li><a href="{$activationUri}">{t}Not received your activation email?{/t}</a></li>
					</ul>
				</div>
			</form>
		</div>

		<div class="floatbar" style="padding-left:100px;">
			<div>
				<a href="https://www.mofilm.com/users/signup" id="registerButton"><span>{t}REGISTER<br />HERE{/t}</span></a>
			</div>

			<h3>{t}Benefits of Registration{/t}</h3>
			<ul class="regBenefits">
				<li>{t}Access to the MOFILM Community{/t}</li>
				<li>{t}Exclusive News{/t}</li>
				<li>{t}Access to Downloads{/t}</li>
			</ul>
		</div>

		<br class="clearBoth" />
	</div>
</div>
</div> <!-- Content Ends -->
</div></div>
{include file=$oView->getTemplateFile('footer','/shared') pageTitle="momusic"}
