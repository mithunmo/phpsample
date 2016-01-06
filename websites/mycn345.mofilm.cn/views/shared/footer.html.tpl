<div id="social"{if $footerClass} class="{$footerClass}"{/if}>
               <div class="container socialcn">
                   <div>
                       <a href="http://i.youku.com/mofilm" target="_blank" class="iconLink"><img src="http://www.mofilm.cn/wp-content/uploads/2013/07/youku.jpg" width="30" class="socialIcon" /></a>
                   </div>
                   <div>
                       <a href="http://www.douban.com/group/mofilm" target="_blank" class="iconLink"><img src="http://www.mofilm.cn/wp-content/uploads/2013/07/douban.png" alt="douban" class="socialIcon" /></a>
                   </div>
                   <div>
                       <a href="http://e.weibo.com/mofilm" target="_blank" class="iconLink"><img src="http://www.mofilm.cn/wp-content/uploads/2013/07/weibo.png" alt="weibo" class="socialIcon" /></a>
                   </div>
                   <div>
                       <a href="http://wpa.qq.com/msgrd?V=1&Uin=1713654449&Site=OK&Menu=yes" target="_blank" class="iconLink"><img src="http://www.mofilm.cn/wp-content/uploads/2013/07/qq.png" alt="qq" class="socialIcon" /></a>
                   </div>
                   <div>
                       <a href="mailto:support@mofilm.cn" target="_blank" class="iconLink"><img src="http://www.mofilm.cn/wp-content/uploads/2013/07/mail.png" alt="email" class="socialIcon" /></a>
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
						<li><a href="http://mofilm.com/info/privacyPolicy">{t}隐私须知{/t}</a></li>
						<li><a href="http://mofilm.com/info/userAgreement">{t}注册用户须知{/t}</a></li>
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
  _gaq.push(['_setAccount', 'UA-4081693-2']);
  _gaq.push(['_setDomainName', 'mofilm.com']);
  _gaq.push(['_setAllowLinker', true]);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

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