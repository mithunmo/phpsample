			<div id="social"{if $footerClass} class="{$footerClass}"{/if}>
				<div class="container social">
					<div>
						<a href="http://www.youtube.com/mofilmchannel#p/u" target="_blank" class="iconLink"><img src="{$themeimages}/logos/youtube-logo.png" width="30" height="30" alt="YouTube" class="socialIcon" /></a>
						{t}MOFILM has a youtube channel - <a href="http://www.youtube.com/mofilmchannel#p/u" target="_blank">watch online</a>{/t}
					</div>
					<div>
						<a href="http://www.facebook.com/mofilm" target="_blank" class="iconLink"><img src="{$themeimages}/logos/fb-logo.png" width="30" height="30" alt="Facebook" class="socialIcon" /></a>
						{t}Be a part of our community on Facebook - <a href="http://www.facebook.com/mofilm" target="_blank">join us</a>{/t}
					</div>
					<div>
						<a href="http://www.twitter.com/MOFILMugc" target="_blank" class="iconLink"><img src="{$themeimages}/logos/twitter-logo.png" width="30" height="30" alt="Twitter" class="socialIcon" /></a>
						{t}If twitter is more your thing then please - <a href="http://twitter.com/MOFILMugc" target="_blank">tweet us</a>{/t}
					</div>
					<div class="clearBoth"></div>
				</div>
			</div>
			
			<div id="footer">
				<div class="container">
					<ul class="col">
						{if $oUser && $oUser->getID() > 0}
							<li><a href="{$mofilmMyUri}/account/profile">{t}Your Profile{/t}</a></li>
							<li><a href="{$mofilmMyUri}/account/pm">{t}Your Messages{/t}</a></li>
						{else}
							<li><a href="{$mofilmMyUri}{$loginUri}">{t}Login{/t}</a></li>
							<li><a href="{$mofilmMyUri}{$registerUri}">{t}Register{/t}</a></li>
							<li><a href="{$mofilmMyUri}{$forgotPasswordUri}">{t}Lost Password{/t}</a></li>
						{/if}
					</ul>
					<ul class="col">
						<li><a href="{$mofilmWwwUri}/info/visitorAgreement">{t}Visitor Agreement{/t}</a></li>
						<li><a href="{$mofilmWwwUri}/info/privacyPolicy">{t}Privacy and Cookie Policy{/t}</a></li>
						<li><a href="{$mofilmWwwUri}/info/userAgreement">{t}Registered User Agreement{/t}</a></li>
						<li><a href="http://eepurl.com/flOh">{t}Subscribe to our Film School Newsletter{/t}</a></li>
					</ul>
					<ul class="col end">
						<li>{t}&copy; Mofilm 2007-{$smarty.now|date_format:'%Y'} All Rights Reserved{/t}</li>
						<li>{t}Usage of this site is governed by our <a href="{$mofilmWwwUri}/info/userAgreement">Terms and Conditions</a>{/t}</li>
					</ul>
					<div class="clearBoth"></div>
				</div>
			</div>
		</div>
		
		<script type="text/javascript" src="/libraries/core_js/core.js"></script>
		<script type="text/javascript" src="/libraries/jquery/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/libraries/jquery-validate/jquery-validate.min.js"></script>
		<script type="text/javascript" src="/libraries/jquery-ui/jquery-ui.min.js"></script>
{foreach $oView->getResourcesByType('js') as $oResource}
		{$oResource->toString()}
{/foreach}
		<script type="text/javascript" src="/libraries/mofilm/lang/{$currentLanguage|default:'en'}.js?{mofilmConstants::JS_VERSION}"></script>
		<script type="text/javascript" src="/libraries/mofilm/mofilm.js?{mofilmConstants::JS_VERSION}"></script>
{if $isProduction}
{literal}    
<script>    
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-4081693-2', 'auto');
ga('send', 'pageview');

</script>
{/literal}
<!-- Start Alexa Certify Javascript -->
<script type="text/javascript">
_atrk_opts = { atrk_acct:"VJHDi1a8Dy00yL", domain:"mofilm.com",dynamic: true};
(function() { var as = document.createElement('script'); as.type = 'text/javascript'; as.async = true; as.src = "https://d31qbv1cthcecs.cloudfront.net/atrk.js"; var s = document.getElementsByTagName('script')[0];s.parentNode.insertBefore(as, s); })();
</script>
<noscript>
<img src="https://d5nxst8fruw4z.cloudfront.net/atrk.gif?account=VJHDi1a8Dy00yL" style="display:none" height="1" width="1" alt="" />
</noscript>
<!-- End Alexa Certify Javascript -->

<!-- Google Code for Remarketing tag -->
<!-- Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. For instructions on adding this tag and more information on the above requirements, read the setup guide: google.com/ads/remarketingsetup -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 992145558;
var google_conversion_label = "esHkCIr4iAUQluGL2QM";
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
{/if}
	</body>
</html>
