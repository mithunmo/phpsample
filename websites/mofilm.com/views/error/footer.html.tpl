			<div id="social">
				<div class="container social">
					<div>
						<a href="http://www.youtube.com/mofilmchannel#p/u" target="_blank" class="iconLink"><img src="{$themeimages}/logos/youtube-logo.png" width="30" height="30" alt="YouTube" /></a>
						{t}MOFILM has a youtube channel - <a href="http://www.youtube.com/mofilmchannel#p/u" target="_blank">watch online</a>{/t}
					</div>
					<div>
						<a href="http://www.facebook.com/mofilm" target="_blank" class="iconLink"><img src="{$themeimages}/logos/fb-logo.png" width="30" height="30" alt="Facebook" /></a>
						{t}Be a part of our community on Facebook - <a href="http://www.facebook.com/mofilm" target="_blank">join us</a>{/t}
					</div>
					<div>
						<a href="http://www.twitter.com/MOFILMugc" target="_blank" class="iconLink"><img src="{$themeimages}/logos/twitter-logo.png" width="30" height="30" alt="Twitter"></a>
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
						<li><a href="{$mofilmWwwUri}/info/visitorAgreement.html">{t}Visitor Agreement{/t}</a></li>
						<li><a href="{$mofilmWwwUri}/info/privacyPolicy.html">{t}Privacy Policy{/t}</a></li>
						<li><a href="{$mofilmWwwUri}/info/userAgreement.html">{t}Registered User Agreement{/t}</a></li>
						<li><a href="http://eepurl.com/flOh">{t}Subscribe to our Film School Newsletter{/t}</a></li>
					</ul>
					<ul class="col end">
						<li>{t}&copy; Mofilm 2007-{$smarty.now|date_format:'%Y'} All Rights Reserved{/t}</li>
						<li>{t}Usage of this site is governed by our <a href="{$mofilmWwwUri}/info/userAgreement.html">Terms and Conditions</a>{/t}</li>
					</ul>
					<div class="clearBoth"></div>
				</div>
			</div>
		</div>
		
		<script type="text/javascript" src="/libraries/core_js/core.js"></script>
		<script type="text/javascript" src="/libraries/jquery/jquery.min.js"></script>
		<script type="text/javascript" src="/libraries/jquery-validate/jquery-validate.min.js"></script>
		<script type="text/javascript" src="/libraries/jquery-ui/jquery-ui.min.js"></script>
		<script type="text/javascript" src="/libraries/mofilm/lang/{$currentLanguage|default:'en'}.js"></script>
		<script type="text/javascript" src="/libraries/mofilm/mofilm.js"></script>
	</body>
</html>