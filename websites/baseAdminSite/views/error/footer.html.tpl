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
						<li><a href="/info/visitorAgreement.html">{t}Visitor Agreement{/t}</a></li>
						<li><a href="/info/privacyPolicy.html">{t}Privacy Policy{/t}</a></li>
						<li><a href="/info/userAgreement.html">{t}Registered User Agreement{/t}</a></li>
					</ul>
					<ul class="col end">
						<li>{t}&copy; Mofilm 2007-{$smarty.now|date_format:'%Y'} All Rights Reserved{/t}</li>
						<li>{t}Usage of this site is governed by our <a href="/info/userAgreement.html">Terms and Conditions</a>{/t}</li>
					</ul>
					<div class="clearBoth"></div>
				</div>
			</div>
		</div>
		
		<script type="text/javascript" src="/libraries/jquery/jquery.min.js"></script>
		<script type="text/javascript" src="/libraries/jquery-validate/jquery-validate.min.js"></script>
		<script type="text/javascript" src="/libraries/jquery-ui/jquery-ui.min.js"></script>
		<script type="text/javascript" src="/libraries/mofilm/lang/{$currentLanguage|default:'en'}.js"></script>
		<script type="text/javascript" src="/libraries/mofilm/admin.js"></script>
	</body>
</html>