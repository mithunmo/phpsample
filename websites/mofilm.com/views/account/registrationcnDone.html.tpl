{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Registration Email Sent{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<div class="floatLeft main">
				<form id="registerForm" action="" method="" name="registerForm" class="dropShadow">
					<h2 class="noMargin">{t}注册邮件已发送{/t}</h2>
					<p>
						{t}感谢您与MOFILM注册{/t}
						{t}我们已经发送一封带有激活您帐户链接的email到<strong>{$email}</strong> 请点击此链接来激活您的帐户.{/t}
						{t}您应该很快收到此email, 如果没有请<a href="{$activationcnUri}">点击这里重新发送</a>.{/t}
					</p>
					<p>
						{t}为什么不加入我们的社区呢? 关注<a href="http://www.weibo.com/mofilm" target="_blank">MOFILM的微博</a>
						或者加入<a href="http://www.douban.com/group/mofilm/" target="_blank">豆瓣小组</a>以便您时不时了解我们最新活动的更新.{/t}
					</p>
					<p>
						{t}<a href="{$logincnUri}">返回登录页面</a>{/t}
					</p>
				</form>
			</div>
			
			<div class="floatLeft sideBar">
				<p class="alignCenter">
					<a href="{$mofilmWwwUri}/competitions/" title="{t}MOFILM: Open Competitions{/t}"><img src="{$themeimages}/competitions-open.jpg" alt="open" /></a>
					&nbsp;&nbsp;
					<a href="{$mofilmWwwUri}/competitions/past" title="{t}MOFILM: Past Competitions{/t}"><img src="{$themeimages}/competitions-past.jpg" alt="past" /></a>
				</p>
			</div>
			
			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}