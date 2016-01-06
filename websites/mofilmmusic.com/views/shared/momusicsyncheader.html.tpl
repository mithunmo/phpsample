<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="Keywords" content="MOFILM, MOMUSIC , MUSIC LIBRARY">		
		<meta name="description" content="momusic.com {if $pageDescription}{$pageDescription}{/if}" />
		<meta name="author" content="{$appAuthor}" />
		<meta name="copyright" content="{$appCopyright}" />
		<meta name="robots" content="noindex, nofollow" />
		<meta name="google-site-verification" content="t6w9SSEXIqyg0Ciakkf7aMU9JhLwUoPHsNjC3GrYG00" />
		<title>{$oRequest->getServerName()} {if $pageTitle}- {$oController->getAction()}{/if}</title>
		<link rel="stylesheet" type="text/css" href="/themes/momusic/css/page.css?{mofilmConstants::CSS_VERSION}" media="screen" />
		<link rel="stylesheet" type="text/css" href="/themes/momusic/css/sidebar.css?{mofilmConstants::CSS_VERSION}" media="screen" />
		<link rel="stylesheet" type="text/css" href="/libraries/jquery-ui/themes/smoothness/jquery-ui.css" media="screen" />
		
		{foreach $oView->getResourcesByType('css') as $oResource}
			{$oResource->toString()}
		{/foreach}
		<link rel="shortcut icon" href="/favicon.ico" />
	</head>
	<body>
		<div class="container">
			<div style="height:115px;"> <!-- Title Starts -->
				<!-- Header gradient with Whale, user icon, logo etc Starts -->
				<div class="gradient">
					<div class="logo"><img src="/themes/momusic/images/img/momusic_logo.png" /></div>
					<div class="right_container">
						<div style="float:left;width:180px;height:30px;padding-top:20px;">
							{if $oLogged}
								<div class="user_icon">Welcome {$oName}</div>
							{/if}							
						</div>
						<div style="float:right;width:50px;height:30px;padding-top:15px;">
								{if $oLogged}
								<div id="noti_Container">
									<a href="/cart/get"><img src="/themes/momusic/images/cart.png" width="32" height="32" alt="cart" /> </a>
									<div class="noti_bubble"></div>
								</div>
								{/if}
							
						</div>
					</div>
					<div class="whale"></div>
				</div>
				<!-- Header gradient with Whale, user icon, logo etc Ends -->
				<!--Spacer start --> <div style="height:7px;"></div><!--Spacer Ends -->

				<!-- Menu Bar Starts -->
				<div  class="gradient2">
					<!-- MAIN MENU STARTS -->
					<div id="nav">
						<ul class="primary">
							<li class="primary"><a href="/music/home" accesskey="1">Home</a></li>
							<li class="primary"><a target="_blank" href="http://www.mofilm.com" accesskey="3">MOFILM</a></li>
							<li class="primary"><a href="/music/help" accesskey="4">FAQ</a></li>
							<li class="primary"><a href="/music/myWork" accesskey="4">My Work</a></li>
							{if $oLogged}
								<li class="primary"><a href="/account/logout" accesskey="5">Logout</a></li>	
							{else}	
								<li class="primary"><a href="/account/login?redirect=/" accesskey="5">Login</a></li>
							{/if}
						</ul>
					</div>
					<!--MAIN MENU ENDS -->
					<!-- Search Div starts-->  
					<form action="/music/sync" method="GET">
						<div class="searchbox">
							<div class="textarea">

								<input class="search_input" placeholder="Find tracks by genre, artists, mood, style, instrument" id="search" name="keyword" onfocus="none"  size="48"  type="text" />


							</div>
							<div style="width:85px; float:right; padding-top:9px;" >
								<input class="button orange searchsync" id="nSearch" type="submit" value="GO" style=" width:52px; height:33px; background-color: #5b5a5a;cursor:pointer;"/>
									<!--a href="#" class="button orange">Go!</a-->

							</div>
						</div>
					</form>	
					<!-- Search Div Ends-->
				</div>
				<!--Menu bar Ends -->
				<div style="height:8px;"></div>
