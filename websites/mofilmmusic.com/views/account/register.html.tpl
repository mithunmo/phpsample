{include file=$oView->getTemplateFile('momusicheader','/shared') pageTitle="momusic"}
<!-- Content Starts --> 
<div style="height:650px;background-image:url(/themes/momusic/images/page_back_border.gif)">
	<div style="width:740px;float:left; height:inherit;">

		{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<div>
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
							<dd>{territorySelect selected=$formData.territory|default:$oCountry->getID() name='territory' class="required"}</dd>
							
							<dt>{t}How did you hear about us?{/t}</dt>
							<dd>
								<select id="SignupCode" name="SignupCode" class="required">
									<option value="">Choose option</option>
									<option value="12">Search Engine</option>
									<option value="13">Facebook</option>
									<option value="14">Facebook Advert</option>
									<option value="15">Video Contest News or Similar</option>
									<option value="16">MOFILM Live Event or Talk</option>
									<option value="17">Friend </option>
								</select>
							</dd>

							<dt>{t}Date of Birth{/t} <sup>1</sup></dt>
							<dd>{html_select_date start_year='1900' field_order='DMY' prefix='' field_array='DateOfBirth' day_value_format='%02d' time=$formData.dob id='dobID'}</dd>
							
							<dt>{t}Skills (Select up to 4 skills){/t}</dt>
							<dd>
								<select name="Skills[]" multiple="multiple" class="valignMiddle string required">
									{foreach $roles as $oRole}
										<option value="{$oRole->getDescription()}">{$trs->__($oRole->getDescription())}</option>
									{/foreach}	
								</select>

							</dd>	
							
						<!--<dt>{t}MOFILM Live! Code{/t} <sup>2</sup></dt>
							<dd><input name="SignupCode" type="text" value="{*$formData.SignupCode*}" class="small" /></dd> -->

							<dt>{t}Receive News and Events{/t} <sup>2</sup></dt>
							<dd><input type="checkbox" name="optIn" value="1" {if !isset($formData) || $formData.optIn}checked="checked"{/if} /></dd>
						
							<dt>&nbsp;</dt>
							<dd>
								<input type="hidden" name="_sk" value="{$formSessionKey}" />
								<input id="regSource" name="registrationSource" type="hidden" value="{$formData.registrationSource}" />
								<input id="facebookID" name="facebookID" type="hidden" value="{$formData.facebookID}" />
								<input type="submit" name="submit" value="{t}Sign Me Up!{/t}" class="submit signup" />

							</dd>
							<dd>
							<div class="registrationNotes">	
									<p>1.{t} You must be 16 or older to enter MOFILM competitions{/t}</p>
									<p>2. {t} From time to time MOFILM would like to send you emails about upcoming competitions, MOFILM Live Events and relevant news from the MOFILM world. Untick the box if you do not want to receive these emails.{/t}</p>
							</div>	
							</dd>	
						</dl>
						
					</div>
				</form>
				
				<br class="clearBoth" />
			</div>
			{*
			<div class="floatRight registerBar">
				<h3>{t}User Registration{/t}</h3>
				<p>
					{t}In order to get full access to the MOFILM website you need to register with us.{/t}
					{t}MOFILM respects your privacy and will not sell your information to third parties or send you spam emails you don't want.{/t}
				</p>
				<hr />
				<p>{t}You should read our <a href="{$mofilmWwwUri}/info/userAgreement.html" title="MOFILM: Registered User Agreement">Registered User Agreement</a> before signing up.{/t}</p>
				<hr />
				<p><a href="{$mofilmMyUri}{$activationUri}">{t}Not received your activation email?{/t}</a></p>
				
				<p class="alignCenter noMargin">
					<a href="{$mofilmWwwUri}/competitions/" title="{t}MOFILM: Open Competitions{/t}"><img src="{$themeimages}/competitions-open.jpg" alt="open" style="width: 90px; height: 90px;" /></a>
					&nbsp;&nbsp;
					<a href="{$mofilmWwwUri}/competitions/past.html" title="{t}MOFILM: Past Competitions{/t}"><img src="{$themeimages}/competitions-past.jpg" alt="past" style="width: 90px; height: 90px;" /></a>
				</p>
			</div>
			*}
			<br class="clearBoth" />
	</div>
</div>
</div> <!-- Content Ends -->
</div></div>
{include file=$oView->getTemplateFile('footer','/shared') pageTitle="momusic"}
