			<div id="social"{if $footerClass} class="{$footerClass}"{/if}>
				<div class="container social">
					<div>
						<a href="http://www.weibo.com/mofilm" target="_blank" class="iconLink"><img src="/themes/mofilm/images/weibo_1.png" width="30" height="30" alt="Weibo" class="socialIcon" /></a>
						{t}MOFILM官方微博 - <a href="http://www.weibo.com/mofilm" target="_blank">关注我们</a>{/t}
					</div>
					<div>
						<a href="http://www.douban.com/group/mofilm" target="_blank" class="iconLink"><img src="/themes/mofilm/images/douban_1.png" width="30" height="30" alt="Twitter" class="socialIcon" /></a>
						{t}MOFILM豆瓣小组 - <a href="http://www.douban.com/group/mofilm" target="_blank">加入小组</a>{/t}
					</div>
					<div class="clearBoth"></div>
				</div>
			</div>
			
			<div id="footer">
				<div class="container">
					<ul class="col">
						{if $oUser && $oUser->getID() > 0}
							<li><a href="/account/profile">{t}账户{/t}</a></li>
							<li><a href="/account/pm">{t}短消息{/t}</a></li>
						{else}
							<li><a href="/account/login">{t}登陆{/t}</a></li>
							<li><a href="/account/register">{t}注册{/t}</a></li>
							<li><a href="/account/forgotpw">{t}丢失密码{/t}</a></li>
						{/if}
					</ul>
					<ul class="col">
						<li><a href="http://www.mofilm.cn/terms-conditions/">{t}访客须知{/t}</a></li>
						<li><a href="http://mofilm.com/info/privacyPolicy.html">{t}隐私须知{/t}</a></li>
						<li><a href="http://mofilm.com/info/userAgreement.html">{t}注册用户须知{/t}</a></li>
						<li><a href="http://eepurl.com/flOh">{t}订阅我们电影学院快讯{/t}</a></li>
					</ul>
					<ul class="col end">
						<li>{t}&copy; Mofilm 2007-{$smarty.now|date_format:'%Y'} 版权所有{/t}</li>
						<li>{t}此网站受该<a href="http://www.mofilm.cn/terms-conditions/">条款管理运行</a>{/t}</li>
					</ul>
					<div class="clearBoth"></div>
				</div>
			</div>
		</div>
		
		<script type="text/javascript" src="/libraries/core_js/core.js"></script>
		<script type="text/javascript" src="/libraries/jquery/jquery.min.js"></script>
		<script type="text/javascript" src="/libraries/jquery-validate/jquery-validate.min.js"></script>
		<script type="text/javascript" src="/libraries/jquery-ui/jquery-ui.min.js"></script>
{foreach $oView->getResourcesByType('js') as $oResource}
		{$oResource->toString()}
{/foreach}
		<script type="text/javascript" src="/libraries/mofilm/lang/{$currentLanguage|default:'en'}.js?{mofilmConstants::JS_VERSION}"></script>
		<script type="text/javascript" src="/libraries/mofilm/mofilm.js?{mofilmConstants::JS_VERSION}"></script>
{if $isProduction}
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-30766241-2']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
{/if}
	</body>
</html>