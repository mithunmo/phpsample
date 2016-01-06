{*
	Notes: Any link needs to be prefixed with either:
	{$mofilmWwwUri} - Use www.mofilm site
	{$mofilmMyUri} - Use my.mofilm site
*}
	<body>
		<div id="header">
			<div class="container">
				<a href="http://mofilm.cn" title="{t}Mofilm Home{/t}" class="mofilmLogo"><span>Mofilm</span></a>
			</div>
		</div>

<div id="nav">
           <ul class="primary">
               <li class="start primary"><a href="http://www.mofilm.cn" accesskey="1">{t}首页{/t}</a></li>
               <li class="dropdown primary">
					<a href="http://www.mofilm.cn/competitions/lastest-competitions/" accesskey="3">{t}Competitions{/t}</a>
                   <ul class="secondary">
						<li class="secondary start"><a href="http://www.mofilm.cn/competitions/lastest-competitions/">最新竞赛</a></li>
						<li class="secondary"><a href="http://www.mofilm.cn/competitions/past-competitions/">{t}过往竞赛{/t}</a></li>
						<li class="secondary"><a href="http://www.mofilm.cn/step/">参赛流程</a></li>
						<li class="secondary end ui-corner-bl ui-corner-br"><a href="http://www.mofilm.cn/about/faq/">常见问题</a></li>
                   </ul>
               </li>
               <li class="dropdown primary">
					<a href="http://www.mofilm.cn/brand/" accesskey="4">{t}品牌合作{/t}</a>
                   <ul class="secondary">
						<li class="secondary"><a href="http://www.mofilm.cn/brand/">业务模式</a></li>
						<li class="secondary"><a href="http://www.mofilm.cn/hall-of-fame/">优秀影片</a></li>
						<li class="secondary"><a href="http://www.mofilm.cn/about/contact/">联系我们</a></li>
						<li class="secondary end ui-corner-bl ui-corner-br"><a href="http://www.mofilm.cn/about/team/">团队介绍</a></li>
                   </ul>
               </li>
               <li class="dropdown primary">
					<a href="http://www.mofilm.cn/news/" accesskey="5">{t}新闻中心{/t}</a>
               </li>
			   
               {nocache}
                   {if $oUser && $oUser->getID() > 0}
                       <li class="dropdown primary end">
							<a href="/account/profile">{t}我的帐户{/t}</a>
							<ul class="secondary">
								<li class="secondary start"><a href="/account/pm">{t}My Messages{/t}</a></li>
								<li class="secondary">
									<a href="/account/myVideo">{t}My Videos{/t}
										<div id="notif_Container">
											<div class="noti_bubble" id="notif_elem"></div>
										</div>
									</a>
								</li>
								<li class="secondary"><a href="/account/grants">{t}My Grants{/t}</a></li>
								<li class="secondary"><a href="/account/upload/plupload">{t}Upload Video{/t}</a></li>
								<li class="secondary">
									<a href="http://my.mofilm.cn/user/crew">寻找团队</a>
								</li>								
								<li class="secondary end ui-corner-bl ui-corner-br"><a href="/account/logout">{t}Logout{/t}</a></li>
							</ul>
						</li>
					{else}
						<li class="dropdown primary">
							<a href="/account/login" accesskey="6">电影人</a>
							<ul class="secondary">
								<li class="secondary"><a href="http://my.mofilm.cn/account/login">登陆/注册</a> </li>
								<li class="secondary end ui-corner-bl ui-corner-br">
									<a href="http://my.mofilm.cn/user/crew">寻找团队</a>
								</li>
							</ul>	
						</li>						
					{/if}
				{/nocache}
			</ul>
		</div>