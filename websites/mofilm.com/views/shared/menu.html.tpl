{*
	Notes: Any link needs to be prefixed with either:
	{$mofilmWwwUri} - Use www.mofilm site
	{$mofilmMyUri} - Use my.mofilm site
*}
	<body>
		<div id="header">
			<div class="container">
				<a href="{$mofilmWwwUri}/" title="{t}Mofilm Home{/t}" class="mofilmLogo"><span>Mofilm</span></a>
			</div>
		</div>

		<div id="nav">
			<ul class="primary">
				<li class="start primary"><a href="{$mofilmWwwUri}/" accesskey="1">{t}Home{/t}</a></li>
				<li class="dropdown primary">
					<a href="{$mofilmWwwUri}/competitions/open" accesskey="3">{t}Competitions{/t}</a>
					<ul class="secondary">
						<li class="secondary start"><a href="{$mofilmWwwUri}/competitions/open">{t}Open Competitions{/t}</a></li>
						<li class="secondary start"><a href="{$mofilmWwwUri}/briefs/open">{t}Live Briefs{/t}</a></li>
						<li class="secondary"><a href="{$mofilmWwwUri}/competitions/past">{t}Past Competitions{/t}</a></li>
						<li class="secondary"><a href="{$mofilmWwwUri}/hall-of-fame">{t}Hall Of Fame{/t}</a></li>
						<li class="secondary end ui-corner-bl ui-corner-br"><a href="{$mofilmWwwUri}/competitions/faq">{t}FAQ{/t}</a></li>
					</ul>
				</li>
				<li class="dropdown primary">
					<a href="{$mofilmWwwUri}/business/" accesskey="4">{t}Business{/t}</a>
					<ul class="secondary">
						<li class="secondary"><a href="{$mofilmWwwUri}/business/index">{t}Welcome to MOFILM{/t}</a></li>
						<li class="secondary"><a href="{$mofilmWwwUri}/business/causes">{t}MOFILMCauses{/t}</a></li>
						<li class="secondary"><a href="{$mofilmWwwUri}/business/marquee">{t}MOFILMMarquee{/t}</a></li>
						<li class="secondary"><a href="{$mofilmWwwUri}/business/music">{t}MOFILMMusic{/t}</a></li>
						<li class="secondary end ui-corner-bl ui-corner-br"><a href="{$mofilmWwwUri}/business/pro">MOFILMpro</a></li>
					</ul>
				</li>
				<li class="primary"><a href="{$mofilmWwwUri}/blog/" accesskey="5">{t}Blog{/t}</a></li>
						<li class="dropdown primary">
							<a href="/account/login" accesskey="7">{t}Filmmaker{/t}</a>
							<ul class="secondary">
							{if !$oUser}	
								<li class="secondary start"><a href="/account/login">{t}Login{/t}</a></li>
								<li class="secondary"><a href="/account/register">{t}Register{/t}</a></li>
							{/if}	
								<li class="secondary"><a href="/user">{t}Profiles{/t}</a></li>
								<li class="secondary end ui-corner-bl ui-corner-br"><a href="/user/crew">{t}MOFILM Crew Builder{/t}</a></li>
							</ul>
						</li>
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
									<a href="/account/myVideo">{t}My Videos / Photos{/t}
										<div id="notif_Container">
											<div class="noti_bubble" id="notif_elem"></div>
										</div>
									</a>
								</li>
								<li class="secondary"><a href="/account/grants">{t}My Grants{/t}</a></li>
								<li class="secondary"><a href="/account/upload">{t}Upload Video{/t}</a></li>
								<li class="secondary end ui-corner-bl ui-corner-br"><a href="/account/logout">{t}Logout{/t}</a></li>
							</ul>
						</li>
					{/if}
				{/nocache}
			</ul>
		</div>
