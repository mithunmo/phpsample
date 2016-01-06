{include file=$oView->getTemplateFile('momusicheader','/shared') pageTitle="Registration Complete"}
<!-- Content Starts --> 
<div style="height:300px;background-image:url(/themes/momusic/images/page_back_border.gif)">
	<div style="width:740px;float:left; height:inherit;">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<div style="padding-left: 100px;">
				<div>
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

					<p><a href="/">{t}Get your Music{/t}</a></p>
				</div>
			</div>
			
			<br class="clearBoth" />
	</div>
</div>
</div> <!-- Content Ends -->
</div></div>
{include file=$oView->getTemplateFile('footer','/shared') pageTitle="momusic"}
