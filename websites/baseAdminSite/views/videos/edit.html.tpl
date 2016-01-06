{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Videos - Edit - {/t}'|cat:$oModel->getMovieID()}
{include file=$oView->getTemplateFile('menu', 'shared')}

<div id="body">
    <div class="container">
        {include file=$oView->getTemplateFile('statusMessage', '/shared')}

        <div class="editTitle">
            <span class="imgWrap"><img src="{$adminEventFolder}/{$oMovie->getSource()->getEvent()->getLogoName()}.jpg" width="50" height="28" border="0" alt="{$oMovie->getSource()->getEvent()->getName()}" title="{t}Event: {/t}{$oMovie->getSource()->getEvent()->getName()}" class="valignMiddle" /></span>
            <span class="imgWrap"><img src="{$adminSourceFolder}/{$oMovie->getSource()->getLogoName()}.jpg" width="50" height="28" border="0" alt="{$oMovie->getSource()->getName()}" title="{t}Source: {/t}{$oMovie->getSource()->getName()}" class="valignMiddle" /></span>
            <h2>{t}Videos{/t} : {$oMovie->getID()} : {$oMovie->getTitle()}</h2>
        </div>

        <form id="movieDetailsForm" class="monitor status{$oMovie->getStatus()}" action="{$doEditURI}" method="post" name="profileForm" enctype="multipart/form-data">
            <div class="hidden">
                <input type="hidden" id="MasterMovieID" name="MovieID" value="{$oMovie->getID()}" />
            </div>
            <div class="floatLeft movieDetails">
                <div class="content">
                    <div class="daoAction">
                        <a href="javascript:history.go(-1);" title="{t}Previous Page{/t}">
                            <img src="{$themeicons}/32x32/action-back.png" alt="{t}Previous Page{/t}" class="icon" />
                            {t}Previous Page{/t}
                        </a>
                        {if $oController->hasAuthority('usersController.message')}
                            <a href="/users/message/{$oMovie->getUserID()}?MovieID={$oMovie->getID()}" title="{t}Message User{/t}">
                                <img src="{$themeicons}/32x32/action-send.png" alt="{t}Message User{/t}" class="icon" />
                                {t}Message User{/t}
                            </a>
                        {/if}
                        {if $oController->hasAuthority('videosController.changeUser')}
                            <a href="/videos/changeUser/{$oMovie->getID()}" title="{t}Change User{/t}">
                                <img src="{$themeicons}/32x32/video-change-user.png" alt="{t}Change User{/t}" class="icon" />
                                {t}Change User{/t}
                            </a>
                        {/if}
                        <button type="reset" name="Cancel" title="{t}Reset Changes{/t}">
                            <img src="{$themeicons}/32x32/action-undo.png" alt="{t}Reset Changes{/t}" class="icon" />
                            {t}Reset Changes{/t}
                        </button>
                        {if $oController->hasAuthority('videosController.doEdit')}
                            <button type="submit" name="UpdateProfile" value="Save" title="{t}Save{/t}" id="myVideoSave">
                                <img src="{$themeicons}/32x32/action-do-edit-object.png" alt="{t}Save{/t}" class="icon" />
                                {t}Save Changes{/t}
                            </button>
                        {/if}
                    </div>
                    <div class="clearBoth"></div>
                </div>

                <div class="content">
                    <div class="mofilmMovieFrame">
                        {if $oMovie->getUploadStatusSet()->getVideoCloudID() > 0 }

                            {if $oMovie->getID() <= 5000 }
                                
                                
                                {if $device == 1 }
                                <video id="my_video_2" class="video-js vjs-default-skin" controls
                                       preload="auto" width="604" height="338" data-setup='{ "techOrder": ["flash"] }'>
                                    <source src="rtmp://s1bzjrwrwz16bm.cloudfront.net/cfx/st&mp4:{$oMovie->getID()}/{$oMovie->getID()}.mp4" type='rtmp/mp4'>
                                </video>
                                {else}
                                <video id="my_video_3" class="video-js vjs-default-skin" controls
                                       preload="auto" width="604" height="338" data-setup='{}'>
                                    <source src="http://s3.amazonaws.com/mofilm-video/{$oMovie->getID()}/{$oMovie->getID()}.mp4" type='video/mp4'>
                                </video>
                                    
                                 {/if}   
                                
                                
                                
                                {*
                                <div id="mofilmMoviePlayer">
                                <!-- Start of Brightcove Player -->
                                <div style="display:none"></div>
                                <div class="flowplayer functional"
                                data-engine="flash"
                                data-rtmp="rtmp://s1bzjrwrwz16bm.cloudfront.net/cfx/st"
                                data-ratio="0.4167">

                                <video autoplay>
                                <source type="video/mp4"  src="mp4:{$oMovie->getID()}/{$oMovie->getID()}">
                                </video>
                                </div>

                                </div>
                                *}           
                            {else}
                                <div id="mofilmMoviePlayer">
                                    <!-- Start of Brightcove Player -->
                                    <div style="display:none"></div>

                                    <object id="myExperience" class="BrightcoveExperience">
                                        <param name="bgcolor" value="#FFFFFF" />
                                        <param name="width" value="604" />
                                        <param name="height" value="338" />
                                        <param name="playerID" value="1667919342001" />
                                        <param name="playerKey" value="AQ~~,AAAA8BM582E~,KSC10SyvF5JMYDrNum2TcfuJnVAPT0mT" />
                                        <param name="isVid" value="true" />
                                        <param name="isUI" value="true" />
                                        <param name="dynamicStreaming" value="true" />
                                        <param name="autoStart" value="false" />                                        
                                        <param name="linkBaseURL" value="{$oMovie->getShortUri($oUser->getID(), true)}" />
                                        <param name="secureConnections" value="true" />
                                        <param name="secureHTMLConnections" value="true" />
                                        <param name="@videoPlayer" value="{$oMovie->getUploadStatusSet()->getVideoCloudID()}" />
                                    </object>
                                </div>
                            {/if}            
                        {else if $oMovie->getSource()->getEvent()->getProductID() == 3 }
                            <div id="mofilmPhotoPlayer">
                                <br/>
                                <br/>
                                <a href="http://adminstage.mofilm.com/resources/_platform/{$oMovie->getID()}/{urlencode(basename($oMovie->getAssetSet()->getObjectByAssetType("File")->getFirst()->getFilename()))}"> Download Idea </a>
                            </div>
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

                        {if $oController->hasAuthority('canRateVideos')}
                            {assign var=oUsrRating value=$oMovie->getUserRating($oUser->getID())}
                            <div id="mofilmAverageRating" class="floatLeft spacer">
                                <strong>{t}Avg Rating:{/t}</strong> (<span id="mofilmMovieAverageRatingCount">{$oMovie->getRatingCount()}</span> {t}ratings{/t})<br />
                                <div id="mofilmMovieAverageRating">
                                    {for $i=0; $i<=10; $i++}
                                        <input type="radio" name="Rating" value="{$i}" {if $i == $oMovie->getAvgRating()}checked="checked"{/if} />
                                    {/for}
                                </div>
                            </div>

                            <div id="movieRatingForm" class="floatRight spacer">
                                <strong>{t}Your Rating:{/t}</strong><br />
                                <div id="mofilmMovieRating">
                                    {for $i=0; $i<=10; $i++}
                                        <input type="radio" name="Rating" value="{$i}" {if $i == $oUsrRating->getRating()}checked="checked"{/if} />
                                    {/for}
                                </div>
                            </div>
                        {/if}
                        <br class="clearBoth" />
                    </div>

                    <br class="clearBoth" />

                    <div id="userFormAccordion">
                        {include file=$oView->getTemplateFile('editMovieDetails')}
                        {if $oController->hasAuthority('editVideoData')}
                            {if $oController->hasAuthority('canSeeMessageHistory')}
                                {include file=$oView->getTemplateFile('editMovieMessageHistory')}
                            {/if}

                            {include file=$oView->getTemplateFile('editMovieProperties')}

                            {include file=$oView->getTemplateFile('editMovieMusic')}

                            {if $oController->hasAuthority('setTags')}
                                {include file=$oView->getTemplateFile('editMovieCategories')}
                            {/if}

                            {if $oController->hasAuthority('canChangeContributors')}
                                {include file=$oView->getTemplateFile('editMovieContributors')}
                            {/if}

                            {if $oController->hasAuthority('canComment')}
                                {include file=$oView->getTemplateFile('editMovieComments')}
                            {/if}

                            {if $oController->hasAuthority('canSeeReviewHistory')}
                                {include file=$oView->getTemplateFile('editMovieReview')}
                            {/if}

                            {if $oController->hasAuthority('canSeeAwardsHistory')}
                                {include file=$oView->getTemplateFile('editMovieAwardsHistory')}
                            {/if}
                            {include file=$oView->getTemplateFile('editMovieMusicLicense')}
                            {if $oController->hasAuthority('canBroadcast')}
                                {include file=$oView->getTemplateFile('editBroadcast')}
                            {/if}
                        {/if}

                        {include file=$oView->getTemplateFile('editMovieCca')}

                        {include file=$oView->getTemplateFile('editMovieAssets')}
                        {include file=$oView->getTemplateFile('editMovieMRSS')}
                    </div>
                </div>
            </div>
        </form>

        <div class="floatLeft movieSidebar">
            <div class="movieTools">
                {if $oUser->isAuthorised('setStatus') && ($oMovie->getStatus() == mofilmMovie::STATUS_PENDING || $oUser->getPermissions()->isRoot())}
                    {strip}
                        <a href="{$statusURI}/{$oMovie->getID()}/Approved{if $oMovie->getStatus() == mofilmMovie::STATUS_PENDING}/sendEmail{/if}" title="{t}Approve Movie{/t}" class="statusUpdate approve">
                            {if $oMovie->getStatus() == mofilmMovie::STATUS_APPROVED}
                                <img src="/themes/shared/icons/tick.png" alt="approved" class="smallIcon" />&nbsp;
                                {t}Approved{/t}
                            {else}
                                {t}Approve{/t}
                            {/if}
                        </a>{/strip}

                        {strip}
                            <a href="{$statusURI}/{$oMovie->getID()}/Rejected" title="{t}Reject Movie{/t}" class="statusUpdate reject">
                                {if $oMovie->getStatus() == mofilmMovie::STATUS_REJECTED}
                                    <img src="/themes/shared/icons/cross.png" alt="rejected" class="smallIcon" />&nbsp;
                                    {t}Rejected{/t}
                                {else}
                                    {t}Reject{/t}
                                {/if}
                            </a>{/strip}
                            {else}
                                <span class="statusUpdate {strip}
                                      {if $oMovie->getStatus() == mofilmMovie::STATUS_APPROVED}
                                          approve
                                      {elseif $oMovie->getStatus() == mofilmMovie::STATUS_REJECTED}
                                          reject
                                      {else}
                                          pending
                                      {/if}
                                      {/strip}">{$oMovie->getStatus()}</span>
                                    {/if}
                                        <div class="clearBoth"></div>
                                    </div>

                                    {include file=$oView->getTemplateFile('addToFavourites','videos') textLabels=true}
                                    
                                    {if ($oController->hasAuthority('canManageProBestOfClient') ||
                                        $oController->hasAuthority('canManageProVideoAwards')) && $eventType['productID'] == '5' }
                                        
                                            {include file=$oView->getTemplateFile('addProAward','videos')}
                                        
                                    {else}
                                        {if ($oController->hasAuthority('canManageBestOfClient') ||
                                             $oController->hasAuthority('canManageVideoAwards')) && $eventType['productID'] != '5'}
                                                {include file=$oView->getTemplateFile('addAward','videos')}
                                        {/if}
                                    {/if}
                                    
                                    

                                    {include file=$oView->getTemplateFile('editMovieStats')}

                                    {include file=$oView->getTemplateFile('profileMiniView', '/account') oUser=$oMovie->getUser() title='{t}User Profile{/t}' movieID=$oMovie->getID()}

                                    {if $oMovie->getReferrer() && $oMovie->getReferredDays() <= 365}
                                        {include file=$oView->getTemplateFile('userReferStats', '/account')}
                                    {/if}

                                    {if $oController->hasAuthority('canSeeUserStats')}
                                        {include file=$oView->getTemplateFile('profileUserStats', '/account') oUser=$oMovie->getUser() title='{t}User Stats{/t}'}
                                    {/if}

                                    {include file=$oView->getTemplateFile('editMovieOtherMovies')}
                                </div>

                                <br class="clearBoth" />
                            </div>
                        </div>

                        <script type="text/javascript">
                            <!--
                        var availableRoles = {$availableRoles};
                            //-->
                        </script>
                        <script type="text/javascript">
                             <!--
                            var countryList = {$countryList};
                             //-->
                        </script>

                        {include file=$oView->getTemplateFile('footer', 'shared')}
