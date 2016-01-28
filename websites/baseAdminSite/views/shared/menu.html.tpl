	<body>                  
                <div id="header">
                    <div class="container">
                        <a href="/home" title="Mofilm Dashboard" class="mofilmLogo"><span>Mofilm</span></a>
                        <div class="userControls">
                        {if $oUser && $oUser->getID() > 0}
                            <div class="loggedIn">
                                <div class="agminmsg">
                                    <div id="notiadmin_Container" style="display: block; width: 40px; height: 0px;">
                                        {$oView->getControllerView('pm', '/account', 'messageCheck')}
                                        {if $messageCount > 0}
                                            <a href="/account/pm/inbox" style="padding:0px;">
                                                <img border="0" style="" src="/themes/mofilm/images/header/msgicon.png" alt="msgicon">
                                            </a>
                                            <div id="MsgCnt" class="notiadmin_bubble">{$messageCount}</div>
                                        {/if}
                                    </div>
                                </div>
                                <span class="options" style="margin-top: -14px; height: 40px;">
                                    <ul style="padding: 0;height: 50px;">
                                        <li class="profileoptions" style="width: 100%;; height: 2px;">
                                            <a href="#">
                                                <div class="profilepix">
                                                    <img src="/themes/mofilm/images/header/profileimg.png" alt="profile" />
                                                </div>
                                                <div class="profilenames" style="">{$oUser->getFirstname()}</div>
                                            </a>
                                            <ul style="width:160px; padding-left: 0;">
                                                <li class="adminmsg"><a href="/account/pm/inbox">Messages</a></li>
                                                <li class="adminpfl"><a href="/account/profile">Your Profile</a></li>
                                                <li class="adminspt"><a href="mailto:support@mofilm.com">Support</a></li>
                                                <li class="adminlgout"><a href="/account/logout"title="Show help page for this request">Log out</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </span>
                            </div>
                        {/if}
                        </div>
                    </div>
                </div>

		<div id="nav">
			<ul class="primary">
				{if $oUser && $oUser->getID() > 0}
					<li class="primary dropdown">
                                            <a href="{system::getConfig()->getParam('mofilm', 'platformUri')->getParamValue()}{'dashboard/?token='}{$accessToken}" accesskey="2">{t}DASHBOARD{/t}</a>
                                            <ul class="secondary">
                                                							
                                                <li class="secondary start"><a href="/home/legacy" accesskey="7">{t}Legacy Dashboard{/t}</a></li>	
                                            </ul>
                                        </li>
					{if $oUser->isAuthorised('admin.menuVideos')}
						<li class="primary dropdown">
							<a href="/videos/" accesskey="3">{t}Videos{/t}</a>
							{if $oUser->isAuthorised('videosController.doSearch') || $oUser->isAuthorised('videosController.review')}
							<ul class="secondary">
								<li class="secondary start"><a href="/videos" accesskey="7">{t}Show All Videos{/t}</a></li>
								{if $oUser->isAuthorised('videosController.doSearch')}
									<li class="secondary start"><a href="/videos/doSearch?Favourites=1&Display=list" accesskey="7">{t}Show Favourites{/t}</a></li>
								{/if}
								
							</ul>
							{/if}
						</li>
					{/if}
                                        {if $oUser->isAuthorised('videosController.doSearch')}
                                                <li class="primary"><a href="/grants" accesskey="7">{t}IDEAS / GRANTS{/t}</a></li>
                                        {/if}
                                        {if $oUser->isAuthorised('admin.menuUsers')}
						<li class="primary"><a href="/uploadFiles/uploadedFilesList" accesskey="4">{t}NDA{/t}</a></li>
					{/if}
                                        {if $oUser->isAuthorised('paymentDetailsController.viewObjects')}
                                                <li class="primary"><a href="/admin/paymentDetails" accesskey="4">{t}PAYMENTS{/t}</a></li>
					{/if}
                                        {if $oUser->isAuthorised('admin.menuUsers')}
                                        <li class="primary"><a href="{system::getConfig()->getParam('mofilm', 'platformUri')->getParamValue()}{'users/?token='}{$accessToken}" accesskey="4">{t}USERS{/t}</a></li>
					{/if}
					
					{if $oUser->isAuthorised('admin.menuReports')}
						<li class="primary"><a href="/reports/" accesskey="5">{t}REPORTS{/t}</a></li>
					{/if}

					{if $oUser->isAuthorised('admin.menuAdmin')}
						<li class="primary"><a href="/admin/" accesskey="6">{t}ADMIN{/t}</a></li>
					{/if}

				{else}
					<li class="primary start"><a href="{$loginUri}">{t}Login{/t}</a></li>
					<li class="primary end"><a href="{$forgotPasswordUri}">{t}Lost Password{/t}</a></li>
				{/if}
			</ul>
		</div>