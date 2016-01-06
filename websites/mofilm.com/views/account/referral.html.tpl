{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}MOFILM Referral Program{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

<div id="body">
	<div class="container">
		{include file=$oView->getTemplateFile('statusMessage', '/shared')}

		<form id="referralForm"action="{$doReferralUri}" method="post" name="activation">
			<h2>{t}MOFILM Referral Program{/t}</h2>
			<div class="floatLeft accountForm">
				<div class="formFieldContainer">
					{t}Please add the email address of the filmmaker you would like to invite to MOFILM, we will then send them an email asking them to join.{/t}
					<br />
					{t}They must click on the link in the email to start the registration process so that we know it was you that referred them.{/t}
					<h3>{t}Refer a Filmmaker!{/t}</h3>
					<div class="field"><input name="refer" type="text" value="" class="string" placeholder="{t}Your Friend’s Email Address{/t}" />
					</div>
					<br />

					<div class="terms" >
						<h3>{t}Filmmaker Referral Program Terms and Conditions{/t}</h3>

						<h4>{t}Please click below to indicate your acceptance of these Terms and Conditions.{/t}</h4>
						<ul>
							<li>{t}By accepting the following Terms and Conditions you agree that from time to time you may refer any person or entity (“Referred Person”) to register on MOFILM Site and participate in MOFILM Competitions by following the instructions on the Site.{/t}</li>
							<li>{t}In the event that any such Referred Person referred by you under pursuant to the Referral Program hereunder is selected as one of the 1st - 5th place winners of any MOFILM Competition, as listed on the Site, within 6 months of such Referred Person’s first date of registration on MOFILM Site, MOFILM shall endeavour to reward you with a one-off gratuitous payment of five hundred US dollars (US$500) (“Referral Fee”).{/t}</li>
							<li>{t}Please follow instructions provided on the Site carefully to ensure that you can qualify for the Referral Fee, as referrals that don't follow instructions or meet the requirements indicated will not be rewarded.{/t}</li>
							<li>{t}Only one payment of Referral Fee per each Referred Person referred by you will be made irrespective of how many times such Referred Person is selected as one of the winners or how many of their films are selected in any Competition. No payment will be made if the Referred Person was registered on MOFILM Site or participated as Filmmaker in any MOFILM Competition or any other MOFILM service prior to the date of your referral.{/t}</li>
							<li>{t}This Referral Program may be terminated by MOFILM at any time at its sole discretion and MOFILM will not make any Referral Fee payments for any referrals that take place after the date of such termination.{/t}</li>
							<li>{t}MOFILM reserves the right, at its sole discretion, to refuse payment of Referral Fee if, in the reasonable opinion of MOFILM, you are either individually or collectively with others: (a) acting in dishonest or disruptive manner in relation to this Referral Program, any Competition or any other activity or service carried on by MOFILM; (b) tampering or attempting to tamper with the entry process or the operation of the Referral Program, any Competition or any Brand Participant or the Site; (b) violating the terms of any relevant rules or agreement(s) between you and MOFILM; (c) violating the terms of service, conditions of use and/or general rules or guidelines of any MOFILM property or service; or (d) acting in an unsportsmanlike or disruptive manner or with intent to annoy, abuse, threaten, harass or cause damage to any other person or with intent to obtain unfair advantage either for yourself or any other person. The decision of MOFILM whether to award payment of Referral Fee is in all cases final and binding and no related correspondence will be entered into.{/t}</li>
							<li>{t}You shall be solely responsible for any taxes on any payment(s) received and agree to complete and return any taxation form received. MOFILM shall not be held responsible for any loss or damage suffered as the result of your or any third party’s participation in this Referral Program or any acceptance or possession of the Referral Fee.{/t}</li>
						</ul>
					</div>
					<br />
					<input type="checkbox" name="field" class="required" />  {t}I agree to MOFILM Filmmaker Referral Program{/t}  <strong>{t}Terms and Conditions{/t} </strong>
					<label class="error" for="field" generated="generated"></label>
					<br />
					<input type="submit" Value="{t}Send Invitation{/t}" class="myReferButton" style="font-size:20px;width:200px;height:35px;" onclick="javascript:if (_gaq) _gaq.push(['_trackEvent','FilmMakerReferral', 'ButtonClick', 'Refer']);" />
				</div>
				<br/>

			</div>

			<br class="clearBoth" />
		</form>
	</div>
</div>

{include file=$oView->getTemplateFile('footer', 'shared')}