<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="author" content="Mofilm tech" />
        <meta name="copyright" content="Mofilm (c) 2009-2010" />
        <meta name="Keywords" content="MOFILM videos, video contest, video contests, online video contests, video competition , Mofilm {$oMovie->getSource()->getEvent()->getName()} videos,Mofilm {$oMovie->getSource()->getName()} videos">
            <meta name="description" content="Mofilm Video titled '{$oMovie->getShortDesc()|xmlstring}' submitted for the event '{$oMovie->getSource()->getEvent()->getName()|trim}' and brand '{$oMovie->getSource()->getName()|trim}' by '{$oUser->getFullname()|trim}'. " />
            <meta name="verify-v1" content="BNIbnUNiV9vkKVGSYRyavz4I3Hf+8CpGeinakuFXwBY=">
                {* Meta tag properties for facebook share *}
                <meta property="og:type" content="movie" />
                <meta property="og:video:type" content="application/x-shockwave-flash" />
                <meta property="og:title" content="{$oMovie->getShortDesc()|xmlstring}" />
                <meta property="og:video:width" content="527" />
                <meta property="og:video" content="{$oMovie->getShortUri($oUser->getID(), true)}" />
                <meta property="og:description" content="Mofilm Video titled '{$oMovie->getShortDesc()|xmlstring}' submitted for the event '{$oMovie->getSource()->getEvent()->getName()|trim}' and brand '{$oMovie->getSource()->getName()|trim}' by '{$oUser->getFullname()|trim}'. " />
                <meta property="og:video:height" content="338" />
                <meta property="og:url" content="{$oMovie->getShortUri($oUser->getID(), true)}" />
                <meta property="og:image" content="{$oMovie->getThumbnailUri('m')}" />

                <title>{$oMovie->getShortDesc()|xmlstring}</title>
                <link rel="stylesheet" type="text/css" href="/themes/mofilm/css/global.css?100098" media="screen" />
                <link rel="stylesheet" type="text/css" href="/themes/mofilm/css/my.css?100098" media="screen" />
                <link rel="stylesheet" type="text/css" href="/libraries/jquery-ui/themes/smoothness/jquery-ui.css" media="screen" />
                <!--[if IE 7]>
                <link rel="stylesheet" type="text/css" href="/themes/mofilm/css/ie7.css?10008" />
                <![endif]-->
                <link rel="stylesheet" type="text/css" href="/themes/mofilm/video-js/video-js.css" media="screen" />
                <link rel="stylesheet" type="text/css" href="/themes/mofilm/css/jquery.fancybox.css" media="screen" /> 
                <link rel="stylesheet" type="text/css" href="/libraries/jquery-ui/themes/smoothness/jquery-ui.css" media="screen" />
                <link rel="stylesheet" type="text/css" href="/libraries/jquery-plugins/smartWizard2/styles/smart_wizard.css" media="screen" />
                <link rel="stylesheet" type="text/css" href="/libraries/jquery-uploadify/uploadify.css" media="screen" />
                <link rel="shortcut icon" href="/favicon.ico" />
                </head>
                {include file=$oView->getTemplateFile('menu','/shared')}

                <div id="body" class="whale">
                    <div class="containerV" style="background-image:url(/themes/mofilm/images/videopage/frame_back_sm1.png);">

                        {*  
                        {if $oModel->isIphone() || $oModel->isIpad()}
                        {if TRUE }
                        <META HTTP-EQUIV="Refresh" Content="0; URL=http://mofilm.com/competitions/watch_video/{$oMovie->getShortUri($oUser->getID(), false)}">;
                        {else}
                        <META HTTP-EQUIV="Refresh" Content="0; URL=http://mofilm.com/competitions/watch_video/{$oMovie->getShortUri($oUser->getID(), false)}">;
                        {/if}
                        {/if}
                        *}  
                        <div style="background-image:url(/themes/mofilm/images/videopage/1pic_back.gif)">
                        </div>
                        <div style="background-image:url(/themes/mofilm/images/videopage/frame_open_sm1.png); background-repeat:no-repeat; vertical-align:top; height:17px;"></div>
                        <div> 
                            <div class="leftcontent">
                                <div class="authname"><h3 style="color:#0066CC;"><strong>Creator: </strong>{$oUser->getFullname()}</h3>
                                </div>
                                <div class="authimage">
                                    {if $oUser->getAvatar()->getImageFilename()}
                                        <a href="/user/{$oUser->getProfile()->getProfileName()}"><img src="{$oUser->getAvatar()->getImageFilename()}" alt="avatar" border="0" width="65" height="64"/></a>
                                    {else}
                                        <a href="/user/{$oUser->getProfile()->getProfileName()}"><img src="{$themeimages}/profile/avatar.jpg" alt="avatar" border="0" width="65" height="64" /></a>
                                    {/if}
                                </div>
                                <div style="clear:both"></div>
                                <div style="width:383px; background-color:#F30; padding-left:10px;">

                                </div>
                                <div class="creditheading">CREATIVE TEAM</div>
                                <div style="height:5px; clear:both; "></div>

                                <div style="clear:both"></div>

                                <div style="text-align:center;">
                                    <table width="364px;" border="0" cellspacing="1" cellpadding="1">
                                        {foreach $oMovie->getContributorSet() as $oContributorMap}
                                            {if $oModel->getValidUser($oContributorMap->getContributor()->getName())}
                                                <tr>
                                                    <td width="20%">
                                                        {if $oModel->getContributingUser($oContributorMap->getContributor()->getName())->getAvatar()->getImageFilename()}
                                                            <a href="/user/{$oModel->getContributingUser($oContributorMap->getContributor()->getName())->getProfile()->getProfileName()}"><img src="{$oModel->getContributingUser($oContributorMap->getContributor()->getName())->getAvatar()->getImageFilename()}" alt="avatar" border="0" width="65" height="64"/></a>
                                                        {else}
                                                            <a href="/user/{$oModel->getContributingUser($oContributorMap->getContributor()->getName())->getProfile()->getProfileName()}"><img src="{$themeimages}/profile/avatar.jpg" alt="avatar" border="0" width="65" height="64" /></a>
                                                        {/if}										
                                                    </td>
                                                    <td width="40%"><strong>{$oModel->getUserName($oContributorMap->getContributor()->getName())}</strong></td>
                                                    <td width="40%"><strong>{$oContributorMap->getRole()->getDescription()}</strong></td>
                                                </tr>
                                                <tr align="center" valign="top">
                                                    <td colspan="3" height="8px;"><div style=" background-image:url(/themes/mofilm/images/videopage/spacer.gif); background-repeat:repeat-x; height:8px;"></div></td>
                                                </tr>

                                            {/if}
                                        {/foreach}
                                    </table>
                                </div>
                            </div>
                            <div class="videospace">
                                <h3 align="center" style="color:#0066CC;">{$oMovie->getShortDesc()|xmlstring}</h3>

                                {if $oMovie->getUploadStatusSet()->getVideoCloudID() > 0 }
                                    <!-- Start of Brightcove Player -->
                                    {if $oMovie->getID() <= 5000 }

                                        {if $device == 1}
                                            <video id="my_video_2" class="video-js vjs-default-skin" controls
                                                   preload="auto" width="527" height="338" data-setup='{ "techOrder": ["flash"] }'>
                                                <source src="rtmp://s1bzjrwrwz16bm.cloudfront.net/cfx/st&mp4:{$oMovie->getID()}/{$oMovie->getID()}.mp4" type='rtmp/mp4'>
                                            </video>

                                        {else}
                                            <video id="my_video_3" class="video-js vjs-default-skin" controls
                                                   preload="auto" width="527" height="338" data-setup='{}'>
                                                <source src="http://s3.amazonaws.com/mofilm-video/{$oMovie->getID()}/{$oMovie->getID()}.mp4" type='video/mp4'>
                                            </video>

                                        {/if}

                                    {else}    


                                        {if $oModel->isIphone() || $oModel->isIpad()}
                                            <META HTTP-EQUIV="Refresh" Content="0; URL=http://mofilm.com/competitions/watch_video/{$oMovie->getShortUri($oUser->getID(), false)}">;
                                            {else}

                                                <div id="mofilmVideoPlayer">
                                                    <div style="display:none"></div>
                                                    <object id="myExperience" class="BrightcoveExperience">
                                                        <param name="bgcolor" value="#FFFFFF" />
                                                        <param name="width" value="527" />
                                                        <param name="height" value="338" />
                                                        <param name="playerID" value="2228227020001" />
                                                        <param name="playerKey" value="AQ~~,AAAA8BM582E~,KSC10SyvF5LDfb0kT9r48e3JK_tD4Man" />
                                                        <param name="isVid" value="true" />
                                                        <param name="isUI" value="true" />
                                                        <param name="dynamicStreaming" value="true" />
                                                        <param name="linkBaseURL" value="http://mofilm.com/video/{$oMovie->getShortUri($oUser->getID(), false)}" />
                                                        <param name="secureConnections" value="true" />
                                                        <param name="secureHTMLConnections" value="true" />
                                                        <param name="@videoPlayer" value="{$oMovie->getUploadStatusSet()->getVideoCloudID()}" />
                                                    </object>
                                                </div>

                                            {/if}                  
                                        {/if}
                                    {else}
                                        <div id="mofilmPhotoPlayer">			    
                                            <div id="gallery">
                                                <div class="album">
                                                    {assign var=imageslist value=$oMovie->getAssetSet()->getObjectByAssetType('Source')->getIterator()}
                                                    {foreach $imageslist as $image}
                                                        <div style="padding: 10px 10px 10px 10px; float: left;">
                                                            <a class="fancybox" data-fancybox-group="gallery" title="{$image->getNotes()}" href="{$image->getFilename()}">
                                                                {assign var=temp value="{$image->getMovieID()}/thumbs"}
                                                                {assign var=thumblink value="{$image->getFilename()|replace:$image->getMovieID():$temp|strstr:".":"true"}"}
                                                                <img src="{$thumblink}.jpg" width="100" height="100" title="{$image->getNotes()}" />
                                                            </a>
                                                        </div>
                                                    {/foreach}
                                                </div>
                                            </div>
                                        </div>

                                    {/if}


                                    <div class="comptitle">MOFILM {if $oMovie->getSource()->getEvent()->getName() != 'Mofilm Closed competition'}: {$oMovie->getSource()->getEvent()->getName()}{/if}</div>

                                    <div style="width:110px; height:69px; float:left; padding-left:155px; text-align:right;">
                                        <div class="compimage"><img src="/resources/client/events/{$oMovie->getSource()->getEvent()->getLogoName()}.jpg" width="100" height="55" alt="dh" /></div>
                                    </div>	
                                    <div style="width:115px; height:69px; float:right; padding-right:140px;">
                                        <div class="compimage"><img src="/resources/client/sources/{$oMovie->getSource()->getLogoName()}.jpg" width="100" height="55" alt="dh" /></div>
                                    </div>
                            </div>
                        </div>
                        <div></div>
                        <br class="clearBoth" />
                    </div>
                    <div style="margin: 0 auto; overflow:hidden; width: 964px; background-image:url(/themes/mofilm/images/videopage/frame_close_sm1.png); background-repeat:no-repeat; height:18px; "> </div>		
                    <div style="padding:20px;" > </div>
                </div>
                {include file=$oView->getTemplateFile('footer','/shared') footerClass='whale'}