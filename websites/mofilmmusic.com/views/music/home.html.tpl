{include file=$oView->getTemplateFile('momusicheader','/shared') pageTitle="momusic"}
<!-- Content Starts --> 
{include file=$oView->getTemplateFile('momusicsidebar','/shared') pageTitle="momusic"}

<div style="width:740px;float:right;background-color: #ecf0f1">
    <div style="font-size:15px;width:711px;height:55px;background-image:url(/themes/momusic/images/img/momusic_banner.jpg);padding-top:221px;color:white;padding-right:15px;padding-left:15px;background-repeat:no-repeat;">

    </div>
    <p style="margin:48px auto;text-align:left; margin-top: 47px; margin-left: -50px; position: relative;font-size:15px; font-weight:bold;margin-top: 52px;">The fast and easy way to see if tracks from our library are right for your video.</p>

    
</div>	
<div style="clear:both;"> </div>
<!------------   RIGHT TOP ENDS ------------------>				


<!------------   MAIN CONTENT START ------------------>				
    <div style=" padding:8px 0 20px 40px;">
<hr style="color:#c2c1c1; margin:20px auto; max-width:100%; text-align:center;" /> 		
        
        <h2 style="margin-bottom:5px;font-weight: bold">Featured Artists</h2>
        <p style="margin:0 0 10px 0;">We feature notable artists from our music library every month. </p>
    </div>				
    
    <div style="clear:both;"> </div>

    {foreach $artistList as $oArtist}    
        <div class="artblk">    
            <img src="{$oModel->getArtistImage($oArtist->getImagePath())}" alt="1" />
            <div class="artxt">
                <h3><a href="/music/solrSearch?artist={urlencode($oArtist->getName())}">{$oArtist->getName()}</a></h3>
                <p>{$oArtist->getDescription()}</p>
            </div>
        </div>
    {/foreach}

    <div style="clear:both;"> </div>
    
<div class="clearfix"></div>




<div style=" padding:8px 0 20px 40px;">
<hr style="color:#c2c1c1; margin:20px auto; max-width:100%; text-align:center;" /> 		
    
    <h2 style="margin-bottom:5px;font-weight: bold">Suggested Tracks For MOFILM Contests</h2>
    <p style="margin:0 0 10px 0;">Here are some tracks from our database that we feel work well with each brief. Click on the relevant brand logo to find the tracks
</p>
</div>		







<div class="bx-wrapper" style="max-width:95%; padding-bottom:20px;">
    <div id="logo_slider" class="everslider logo-slider" style="margin:0 auto;">
        <ul class="es-slides">
            {assign var="briefs" value=$oModel->getOpenBriefs()}
            {foreach $brandMusic as $oBrand}
                {if $oModel->isBriefOpen($oBrand->getBrandID())}
                <li>
                    <a href="/music/brief?brandMusicID={$oBrand->getID()}"><img src="http://mofilm.com/img/resources/client/sources/logo/{$oBrand->getBrandID()}.png"></a>
                        {assign var="name" value=$oModel->getBrandName($oBrand->getBrandID())}
                    <div class="logoestxt"> {$name} </div>
                </li>
                {/if}

            {/foreach}    


        </ul> 
    </div>

    <!-- control -->
    <div class="bx-controls-direction">
        <a class="bx-prev2">Prev</a>
        <a class="bx-next2">Next</a>
    </div>
    <!-- control end -->

</div>
    
    
<div style=" padding:8px 0 20px 40px;">
<hr style="color:#c2c1c1; margin:20px auto; max-width:100%; text-align:center;" /> 		
    
    <h2 style="margin-bottom:5px;font-weight: bold;">Handpicked Music</h2>
    <p style="margin:0 0 10px 0;">These are some of the most popular tracks hand-picked by our music specialists
 </p>
</div>			


<div class="bx-wrapper" style="max-width:95%;">
    <div id="artist_slider" class="everslider logo-slider" style="margin:0 auto;">
        <ul class="es-slides">

            {foreach $handpickedMusic as $oMusic}    
                <li>
                    <a  rel="nofollow" href="{$oModel->getTrackInfo($oMusic->getTrackID())->getPath()}" title="Play &quot;Change&quot;" class="sm2_button exclude inline-exclude norewrite momusicsong">
                        
                        {*<img src="/themes/momusic/images/img/artistpix.jpg">*}                      
                    </a>
                    <img src="{$oModel->getCoverImage($oMusic->getCoverImageID())}">
                    <div style="margin-left:5px;"> <div style="margin-top:3px;margin-bottom:0;font-size: 12px;font-weight: bold">{$oModel->getTrackInfo($oMusic->getTrackID())->getSongName()}</div>
                        <div style="font-size:12px; margin:0;">
                        {assign var="artistName" value=$oModel->getTrackInfo($oMusic->getTrackID())->getArtistName()}
                        <a href="/music/solrSearch?artist={urlencode($artistName)}">{$artistName}</a>
                        </div>
                        
                    
                    </div>
                </li>
            {/foreach}

        </ul> </div>

    <!-- control -->
    <div class="bx-controls-direction">
        <a class="bx-prev1">Prev</a>
        <a class="bx-next1">Next</a>
    </div>
    <!-- control end -->

</div> 
<!-- artist slider end -->	
<!-- logo slider end -->


<hr style="color:#c2c1c1; margin:40px auto; max-width:80%; text-align:center;" /> 		
<div style="padding:8px 0 20px 20px; text-align:center; max-width:75%; margin:0 auto;">
    <h1 style="margin-bottom:5px;">For Brands: A Full Service Music Agency</h1>
    <p style="margin:0 0 10px 0;">MOMUSIC offers a wide range of services to help identify, secure, or compose the right track for any ad or branded content. </p>
    <div><p>&nbsp;</p></div>

    <a class="small radius button orang-btn" href="http://mofilm.com/business/music" style="width:164px;">Learn more</a>
    
</div>			
<div style="clear:both;"> </div>

<div style="height:30px;"> </div>

<div>
</div> <!-- Content Ends -->


{include file=$oView->getTemplateFile('momusicfooter','/shared') pageTitle="momusic"}
