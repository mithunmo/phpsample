{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Registration Complete{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<div class="floatLeft main">
				<div id="registerForm">
					<h2 class="noMargin">{t}注册成功{/t}</h2>

					<p>{t}欢迎来到MOFILM的世界.{/t}</p>
					{if !$oUser->hasPassword()}
						<p>
							{t}Thanks for confirming your registration.{/t}<br />
							{t}We just need you to choose a password for your login or you won't be able to login again!{/t}
						</p>
						<p><a href="/account/profile">{t}Set your password now via the profile page.{/t}</a>
					{/if}

					<h3>更多</h3>
					<p>
						为什么不加入我们的社区呢? 关注 <a href="http://www.weibo.com/mofilm"  title="weibo">MOFILM的微博</a>
						或者加入 <a href="http://www.douban.com/group/mofilm/"  title="douban">豆瓣小组</a>以便您时不时了解我们最新活动的更新.
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

					<p><a href="/account/profile">{t}账户页面<{/t}</a></p>
				</div>
			</div>
			<div class="floatRight registerBar">
				<p class="alignCenter noMargin">
					{foreach $oEvents as $oEvent}
						{if $oEvent->getID() != 4 && $oEvent->getID() != 22 && $oEvent->getID() != 21}
							<a href="http://mofilm.cn{$oEvent->getWebPath()}" title="{t}MOFILM: Open Competitions{/t}"><img src="{$oSmallImagePath}{$oEvent->getID()}.jpg" alt="open" style="width: 300px; height: 150px;" /></a>
							<br />
						{/if}
					{/foreach}	
				</p>
				
			</div>	
			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}