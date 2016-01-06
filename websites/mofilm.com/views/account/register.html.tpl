{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Register New User{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<div class="floatLeft mainRegister">
				<form id="registerForm" action="{$doRegisterUri}" method="post" name="registerForm" class="dropShadow">
					<h1 class="noMargin">{t}Sign Up to MOFILM{/t}</h1>
					<p>{t}You need to register with MOFILM to continue.{/t}</p>
					
					<div id="profilecontents">
						<dl>

							<dt>{t}Username{/t}</dt>
							<dd><input id="profileName" name="ProfileName" type="text" value="{$formData.ProfileName}" class="required string" /></dd>

							<div id="nameField">
									<dt>{t}Firstname{/t}</dt>
									<dd><input id="firstName" name="Firstname" type="text" value="{$formData.Firstname}" class="required string" /></dd>

									<dt>{t}Surname{/t}</dt>
									<dd><input id="surName" name="Surname" type="text" value="{$formData.Surname}" class="required string" /></dd>
							</div>
						
							<dt>{t}Email Address{/t}</dt>
							<dd><input id="emailAddress" name="username" type="text" value="{$formData.username}" class="required string" /></dd>
					
							<dt>{t}Password{/t}</dt>
							<dd><input name="Password" type="password" class="required" /></dd>
						
							<dt>{t}Confirm Password{/t}</dt>
							<dd><input name="ConfirmPassword" type="password" class="required" /></dd>
						
							<div id="cityField">
								{if $formData.City}
									<dt>{t}City{/t}</dt>
									<dd><input name="City" type="text" value="{$formData.City}" class="string" /></dd>
								{/if}
							</div>
							
							<dt>{t}Phone Number{/t}<dt>
							<dd><input name="Phone" type="text" value="{$formData.Phone}" class="required" /></dd>
							    
							<dt>{t}Country{/t}</dt>
							<dd>{territorySelect selected=$formData.territory|default:$oCountry->getID() name='territory'}</dd>
							
							<dt>{t}How did you hear about us ?{/t}</dt>
							<dd>{signupCodeSelect selected=$formData.SignupCode name='SignupCode'}</dd>
							
							<dt>{t}Skills (Select up to 4 skills){/t}</dt>
							<dd>
								<select name="Skills[]" multiple="multiple" class="valignMiddle string required">
									{foreach $roles as $oRole}
										<option value="{$oRole->getDescription()}">{$trs->__($oRole->getDescription())}</option>
									{/foreach}	
								</select>

							</dd>	
							
							<dt>{t}School Name{/t}<dt>
							<dd><input name="SchoolName" type="text" value="{$formData.SchoolName}" class="string" /></dd>
							
							<dt>{t}Date of Birth{/t} <sup>1</sup></dt>
							<dd>{html_select_date start_year='1900' field_order='DMY' prefix='' field_array='DateOfBirth' day_value_format='%02d' time=$formData.dob id='dobID'}</dd>
							
						<!--<dt>{t}MOFILM Live! Code{/t} <sup>2</sup></dt>
							<dd><input name="SignupCode" type="text" value="{*$formData.SignupCode*}" class="small" /></dd> -->

							<dt>{t}Receive News and Events{/t} <sup>2</sup></dt>
							<dd><input type="checkbox" name="optIn" value="1" {if !isset($formData) || $formData.optIn}checked="checked"{/if} /></dd>
							
							<div>
								<dt><div style="float:right; padding-right: 20px; padding-top: 15px;">{t}Enter the Code shown{/t}</div><dt>
								<dd>
									<div style="float:right; padding-right: 244px;"><img src="/captcha" alt="image" /></div>
									<div style="float:right; padding-right: 10px; padding-top: 15px;"><input name="Captcha" type="text" value="" class="small required" /></div>
								</dd>
							</div>
							<div style="clear:both;"></div>
							
							<dt>&nbsp;</dt>
							<dd>
								<input type="submit" name="submit" value="{t}Sign Me Up!{/t}" class="submit signup" />
								<input type="hidden" name="_sk" value="{$formSessionKey}" />
                                                                <input type="hidden" name="Affiliate" value="{$affiliate}" />
                                                                <input type="hidden" name="redirect" value="{$formData.redirect}" />
								<input id="regSource" name="registrationSource" type="hidden" value="{$formData.registrationSource}" />
								<input id="facebookID" name="facebookID" type="hidden" value="{$formData.facebookID}" />
							</dd>
						</dl>
					
						<div id="fb-root" style="padding: 0px 0px 0px 0px;"></div>
						{if $formData.registrationSource != 'facebook'}
						    
							<div id="fb_link_display" style="display: none">
								<div class="registerFbLink"><a class="fb_button fb_button_medium"><span class="fb_button_text">Register using Facebook</span></a></div>
							</div>
						    
							<div id="fb_button_display" style="display: none">
								<fb:login-button autologoutlink="true" scope="email,user_birthday,publish_stream">Register using Facebook</fb:login-button>
							</div>
						{/if}
					</div>
					<ol class="registrationNotes">
						<li>{t}You must be 16 or older to enter MOFILM competitions{/t}</li>
						<li>{t}From time to time MOFILM would like to send you emails about upcoming competitions, MOFILM Live Events and relevant news from the MOFILM world. Untick the box if you do not want to receive these emails.{/t}</li>
					</ol>
				</form>
				
				<br class="clearBoth" />
			</div>
			
			<div class="floatRight registerBar">
				<h3>{t}User Registration{/t}</h3>
				<p>
					{t}In order to get full access to the MOFILM website you need to register with us.{/t}
					{t}MOFILM respects your privacy and will not sell your information to third parties or send you spam emails you don't want.{/t}
				</p>
				<hr />
				<p>{t}You should read our <a href="{$mofilmWwwUri}/info/userAgreement" title="MOFILM: Registered User Agreement">Registered User Agreement</a> before signing up.{/t}</p>
			
				
				<p class="alignCenter noMargin">
					<a href="{$mofilmWwwUri}/competitions/open" title="{t}MOFILM: Open Competitions{/t}"><img src="{$themeimages}/competitions-open.jpg" alt="open" style="width: 90px; height: 90px;" /></a>
					&nbsp;&nbsp;
					<a href="{$mofilmWwwUri}/competitions/past" title="{t}MOFILM: Past Competitions{/t}"><img src="{$themeimages}/competitions-past.jpg" alt="past" style="width: 90px; height: 90px;" /></a>
				</p>
			</div>
			
			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}