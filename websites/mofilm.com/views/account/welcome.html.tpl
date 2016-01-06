{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Registration Complete{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<div class="floatLeft main">
				<div id="registerForm">
					<h2 class="noMargin">{t}Registration Complete{/t}</h2>

					<p>{t}Welcome to the MOFILM world.{/t}</p>
					{if !$oUser->hasPassword()}
						<p>
							{t}Thanks for confirming your registration.{/t}<br />
							{t}We just need you to choose a password for your login or you won't be able to login again!{/t}
						</p>
						<p><a href="/account/profile">{t}Set your password now via the profile page.{/t}</a>
					{/if}

					<h3>{t}More from MOFILM{/t}</h3>
					<p>
						{t}Why not sign up to our <a href="http://www.facebook.com/pages/MOFILM/123362452780" target="_blank">Facebook</a>
						or <a href="http://www.twitter.com/MOFILMugc" target="_blank">Twitter</a> communities while you are here, or use the menu above to navigate.{/t}
					</p>

					{if $oUser->getPermissions()->isAuthorised('admin.canLogin')}
						<p>
							{t}As an admin user you can login at:{/t}
							{if $isProduction}
								<a href="http://admin.mofilm.com/">http://dev.admin.mofilm.com</a>
							{else}
								<a href="http://dev.admin.mofilm.com/">http://admin.mofilm.com</a>
							{/if}
						</p>
						{if !$oUser->hasPassword()}
							<p>{t}You must set a password before you can login to the admin system.{/t} <a href="/account/profile">{t}Set one now{/t}</a></p>
						{/if}
					{/if}

					<p><a href="/account/profile">{t}Continue to profile{/t}</a></p>
				</div>
			</div>
			
			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}