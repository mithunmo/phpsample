{*
	Notes: Any link needs to be prefixed with either:
	{$mofilmWwwUri} - Use www.mofilm site
	{$mofilmMyUri} - Use my.mofilm site
*}
	<body>
		<div style="background: #000a3e; width:auto; height:62px;" >
<div style="width:940px;background: #000a3e;margin: 0 auto; height:62px; background-image:url(/themes/mofilm/images/momusic/w1.jpg); background-repeat:no-repeat;">
<div style="float:left; width:250px; height:60px; padding-top:5px;"><img src="/themes/mofilm/images/momusic/mo_music_logo.png" border="0px;" /></div>
<div style="float: right; height:30px; padding-top:30px; font-family:'Trebuchet MS', Arial, Helvetica, sans-serif; color:#FFF; width: 370px;">HOME&nbsp;&nbsp;&nbsp; |&nbsp;&nbsp;&nbsp; MOFILM&nbsp;&nbsp;&nbsp;| &nbsp;&nbsp;&nbsp; ABOUT &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; FAQ</div>
</div>
</div>{*
		<div id="nav">
			<ul class="primary">
				<li class="start primary"><a href="{$mofilmWwwUri}/" accesskey="1">{t}Home{/t}</a></li>
				<li class="dropdown primary">
					<a href="{$mofilmWwwUri}/competitions/" accesskey="3">{t}Competitions{/t}</a>
					<ul class="secondary">
						<li class="secondary start"><a href="{$mofilmWwwUri}/competitions/">{t}Open Competitions{/t}</a></li>
						<li class="secondary"><a href="{$mofilmWwwUri}/competitions/past.html">{t}Past Competitions{/t}</a></li>
						<li class="secondary"><a href="{$mofilmWwwUri}/competitions/pepsifilms.html">{t}Pepsi Films{/t}</a></li>
						<li class="secondary end ui-corner-bl ui-corner-br"><a href="{$mofilmWwwUri}/competitions/faq.html">{t}FAQ{/t}</a></li>
					</ul>
				</li>
				<li class="dropdown primary">
					<a href="{$mofilmWwwUri}/business/" accesskey="4">{t}Business{/t}</a>
					<ul class="secondary">
						<li class="secondary"><a href="{$mofilmWwwUri}/about/causes/">{t}Causes{/t}</a></li>
						<li class="secondary"><a href="{$mofilmWwwUri}/business/partners/">{t}Partners{/t}</a></li>
						<li class="secondary"><a href="{$mofilmWwwUri}/business/social/">{t}Social{/t}</a></li>
						<li class="secondary end ui-corner-bl ui-corner-br"><a href="{$mofilmWwwUri}/business/pro/">MOFILMpro</a></li>
					</ul>
				</li>
				<li class="primary"><a href="{$mofilmWwwUri}/blog" accesskey="5">{t}News{/t}</a></li>
				<li class="dropdown primary">
					<a href="{$mofilmWwwUri}/about" accesskey="7">{t}About{/t}</a>
					<ul class="secondary">
						<li class="secondary start"><a href="{$mofilmWwwUri}/about/team/">{t}Meet The Team{/t}</a></li>
						<li class="secondary end ui-corner-bl ui-corner-br"><a href="{$mofilmWwwUri}/about/contact/">{t}Contact Us{/t}</a></li>
					</ul>
				</li>
				{nocache}
					{if $oUser && $oUser->getID() > 0}
						<li class="dropdown primary end">
							<a href="/account/profile">{t}My Account{/t}</a>
							<ul class="secondary">
								<li class="secondary start"><a href="/account/pm">{t}My Messages{/t}</a></li>
								<li class="secondary">
									<a href="/account/myVideo">{t}My Videos{/t}
										<div id="notif_Container">
											<div class="noti_bubble" id="notif_elem"></div>
										</div>
									</a>
								</li>
								<li class="secondary"><a href="/account/upload">{t}Upload Video{/t}</a></li>
								<li class="secondary end ui-corner-bl ui-corner-br"><a href="/account/logout">{t}Logout{/t}</a></li>
							</ul>
						</li>
					{else}
						<li class="primary end"><a href="/account/login">{t}Login{/t}</a></li>
					{/if}
				{/nocache}
			</ul>
		</div>
		*}