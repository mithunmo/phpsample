{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Registration Email Sent{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}
{literal}
	<style type="text/css">
		#registerForm {background: url(/themes/mofilm/images/mofilmcn/login-bg-fb.jpg) no-repeat; }
	</style>
{/literal}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<div class="floatLeft main">
				<form id="registerForm" action="" method="" name="registerForm" class="dropShadow">
					<h2 class="noMargin">已发送注册邮件</h2>
					<p>
						感谢您与MOFILM注册.</p>
						<p>我们已发送邮件到<strong>test@dispostable.com</strong> 其带有一个链接来激活您的账户.</p>
						<p>你将很快收到一封邮件，如果没有你没有收到 <a href="{$activationUri}">请点击这里重新发送</a>.
					</p>
					<p>
						获得并了解最新MOFILM活动信息请关注我们 <a href="http://www.weibo.com/mofilm" target="_blank">微博</a>
                       或者 <a href="http://www.douban.com/group/mofilm/" target="_blank">豆瓣</a> 社区
					</p>
					<p>
						
						<a href="{$loginUri}">返回登陆页面</a>
					</p>	
					
				</form>
			</div>
			
			<div class="floatRight sideBar">
				<p class="alignCenter">
					<a href="{$mofilmWwwUri}/competitions/" title="{t}MOFILM: Open Competitions{/t}"><img src="{$themeimages}/competitions-open.jpg" alt="open" /></a>
					&nbsp;&nbsp;
					<a href="{$mofilmWwwUri}/competitions/past" title="{t}MOFILM: Past Competitions{/t}"><img src="{$themeimages}/competitions-past.jpg" alt="past" /></a>
				</p>
					<p>
					<a href="http://bcsff.mofilm.cn/" title="MOFILM北京大学生电影节竞赛单元"><img src="/themes/mofilm/images/mofilmcn/bcsfficon.jpg" alt="bcsff" ></a>
					</p>
				
			</div>
			
			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}