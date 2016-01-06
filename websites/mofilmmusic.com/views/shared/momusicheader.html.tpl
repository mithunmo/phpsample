<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="Keywords" content="MOFILM, MOMUSIC , MUSIC LIBRARY, Stock Music Library, Pre-cleared songs, music for commercials, music for videos, Submit music, Music Licensing, music licensing store, production music, video music, music for ads, music download, audio video sync, music library">		
        <meta name="description" content="momusic.com {if $pageDescription}{$pageDescription}{/if}" />
        <meta name="robots" content="NOINDEX, NOFOLLOW" />		
        <meta name="author" content="{$appAuthor}" />
        <meta name="copyright" content="{$appCopyright}" />
        <meta name="google-site-verification" content="t6w9SSEXIqyg0Ciakkf7aMU9JhLwUoPHsNjC3GrYG00" />
        <title> MOMUSIC {if $pageTitle}- {$oController->getAction()}{/if}</title>
        <link rel="stylesheet" type="text/css" href="{$themefolder}/css/mymomusic.css?{mofilmConstants::CSS_VERSION}" media="screen" />

        <link rel="stylesheet" type="text/css" href="/themes/momusic/css/page.css?{mofilmConstants::CSS_VERSION}" media="screen" />
        <link rel="stylesheet" type="text/css" href="/themes/momusic/css/sidebar.css?{mofilmConstants::CSS_VERSION}" media="screen" /> 
        <link rel="stylesheet" type="text/css" href="/libraries/jquery-ui/themes/smoothness/jquery-ui.css" media="screen" />

        {foreach $oView->getResourcesByType('css') as $oResource}
            {$oResource->toString()}
        {/foreach}
        <link rel="shortcut icon" href="/favicon.ico" />		
        <!------ NEW ADDED CSS JS & inline CSS----->
        <link href="/themes/momusic/css/everslider.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="http://www.mofilm.com/css/jquery.bxslider.css" />
        <style>.bx-wrapper{ padding-left: 15px; padding-right: 15px;}</style>
    </head>
    <body>
        <div style="right: 1px; position: fixed; top: 30%; width: 30px; z-index: 10000" >
            <a class="lightbox-30621187014949" style="cursor:pointer;color:blue;text-decoration:underline;"><img height=128 width=32 src="/themes/momusic/images/feedback.png"></a>	</div>
        <div class="container">
            <div style="height:115px;"> <!-- Title Starts -->
                <!-- Header gradient with Whale, user icon, logo etc Starts -->
                <div class="gradient" style="color:white;">
                    <div class="logo" style="width:196px; height:55px; ">
                        <a href="/"><img src="/themes/momusic/images/img/momusic_logo.png">	</a>
                    </div>
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
                            <li class="primary"><a href="/music/help" accesskey="6">FAQ</a></li>
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
                    <form action="/music/result" method="GET">
                        <div class="searchbox">
                            <div class="textarea">

                                <input class="search_input" placeholder="Find tracks by genre, artists, mood, style, instrument" id="search" name="keyword" onfocus="none"  value="{$search}" size="48"  type="text" />


                            </div>
                            <div style="width:85px; float:right; padding-top:9px;" >
                                <input class="button orange" id="nSearch" type="submit" value="GO" style=" width:52px; height:33px; background-color: #5b5a5a;cursor:pointer;">
                                <!--a href="#" class="button orange">Go!</a-->

                            </div>
                        </div>
                    </form>	
                    <!-- Search Div Ends-->
                </div>
                <!--Menu bar Ends -->
                <div style="height:8px;"></div>
