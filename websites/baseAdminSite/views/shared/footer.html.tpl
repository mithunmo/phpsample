			<div id="footer">
				<div class="container">
					<ul class="col">
						{if $oUser && $oUser->getID() > 0}
							<li><a href="/home">{t}Dashboard{/t}</a></li>
							<li><a href="/account/profile">{t}Your Profile{/t}</a></li>
							<li><a href="/videos">{t}Videos{/t}</a></li>
						{else}
							<li><a href="{$loginUri}">{t}Login{/t}</a></li>
							<li><a href="{$forgotPasswordUri}">{t}Lost Password{/t}</a></li>
						{/if}
					</ul>
					<ul class="col">
						<li><a href="http://www.mofilm.com/info/visitorAgreement">{t}Visitor Agreement{/t}</a></li>
						<li><a href="http://www.mofilm.com/info/privacyPolicy">{t}Privacy Policy{/t}</a></li>
						<li><a href="http://www.mofilm.com/info/userAgreement">{t}Registered User Agreement{/t}</a></li>
					</ul>
					<ul class="col end">
						<li>{t}&copy; Mofilm 2007-{$smarty.now|date_format:'%Y'} All Rights Reserved{/t}</li>
						<li>{t}Usage of this site is governed by our <a href="http://www.mofilm.com/info/userAgreement">Terms and Conditions</a>{/t}</li>
					</ul>
					<div class="clearBoth"></div>
				</div>
			</div>
		</div>
		
		<script type="text/javascript" src="/libraries/core_js/core.js"></script>
		<script type="text/javascript" src="/libraries/jquery/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/libraries/jquery-validate/jquery-validate.min.js"></script>
		<script type="text/javascript" src="/libraries/jquery-ui/jquery-ui.min.js"></script>
		<script type="text/javascript" src="/libraries/jquery-plugins/thickbox.js"></script>
{foreach $oView->getResourcesByType('js') as $oResource}
		{$oResource->toString()}
{/foreach}
		<script type="text/javascript" src="/libraries/mofilm/lang/{$currentLanguage|default:'en'}.js?{mofilmConstants::JS_VERSION}"></script>
		<script type="text/javascript" src="/libraries/mofilm/admin.js?{mofilmConstants::JS_VERSION}"></script>
	</body>
</html>