{include file=$oView->getTemplateFile('momusicheader','/shared') pageTitle="momusic"}
<!-- Content Starts --> 
	{include file=$oView->getTemplateFile('momusicsidebar','/shared') pageTitle="momusic"}
	<div style="width:740px;float:right;  height:inherit;">
		
	<h1> MOMUSIC FAQ </h1>


	<h3>Tutorial to use the site </h3>	
				<div style="width:500px;  height:280px;">
					<object id="flashObj" width="500" height="280" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,47,0">
						<embed src="http://c.brightcove.com/services/viewer/federated_f9?isVid=1&isUI=1" bgcolor="#000000" flashVars="@videoPlayer=ref:18503&playerID=2212869319001&playerKey=AQ~~,AAAA8BM582E~,KSC10SyvF5L79vPFmpaOlEYXmkwA8N29&domain=embed&dynamicStreaming=true" base="http://admin.brightcove.com" name="flashObj" width="500" height="280" seamlesstabbing="false" type="application/x-shockwave-flash" allowFullScreen="true" allowScriptAccess="always" swLiveConnect="true" pluginspage=""></embed>
					</object>
				</div>
		
	<div style="height:10px;"> </div>
	
	<h3>Tutorial to use the Video/Audio SYNC tool </h3>		
				<div style="width:500px;  height:280px;">
					<object id="flashObj" width="500" height="280" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,47,0">
						<embed src="http://c.brightcove.com/services/viewer/federated_f9?isVid=1&isUI=1" bgcolor="#000000" flashVars="@videoPlayer=ref:18505&playerID=2212869319001&playerKey=AQ~~,AAAA8BM582E~,KSC10SyvF5L79vPFmpaOlEYXmkwA8N29&domain=embed&dynamicStreaming=true" base="http://admin.brightcove.com" name="flashObj" width="500" height="280" seamlesstabbing="false" type="application/x-shockwave-flash" allowFullScreen="true" allowScriptAccess="always" swLiveConnect="true" pluginspage=""></embed>
					</object>
				</div>
	

		<div> 
			<p>
			<h3>What is MOMUSIC Sync Player?</h3>
			MOMUSIC sync player is an online tool that helps you the select the best music tracks for your video project.  It lets you to preview as to how well the selected music tracks will mash with your video before buying/downloading.
			</p>
			<p>
			<h3>How can I upload my video?</h3>
			You can upload the video by clicking on the ‘Upload Video’ button on the sync player.  This will open up a file select window where you can select the video file that you want to upload. 
			Once the upload is done two assets are seen in the uploaded video tab .One with the filename ( audio suppressed ) and the other with VO_filename ( with audio ) .
			Drag the required video to the timeline . 
			</p>
			<p>
			<h3>Which video file types are supported?</h3>
			Currently only FLV,MP4 and MOV files are supported.
			</p>
			<p>
			<h3>Can I upload multiple video?</h3>
			Yes.
			</p>
			<p>
			<h3>How can I play the video?</h3>
			First upload your video .The video will reside in the uploaded video section of momusic player .Select your video and drag it to the
			timeline . Click on the play button .
			</p>
			<p>
			<h3>Is my uploaded video publicly viewable?</h3>
			No.  Your uploaded videos are private to you.  But if you chose to share your work by generating a pubic link (see below), then it is viewable by everyone who has the public link to your shared work.
			</p>
			<p>
			<h3>How can I share my work with others?</h3>
			You can share your work by creating a public link to your work.  You can create a public link by clicking on the ‘Save’ button and then copying the link on the browser.  You can send this public link to the people whom you want to share.
			</p>
			<p>
			<h3>How can I preview the music?</h3>
			You can preview music by clicking on the blue arrow just before the track name.
			</p>
			<p>
			<h3>How do I select music to sync with my video?</h3>
			You can select the music by clicking on the ‘Add’ checkbox next to the music.
			</p>
			<p>
			<h3>How do I clear my uploaded video?</h3>
			You can clear your uploaded video by clicking on the ‘Clear Workspace’ button.  Once cleared, you need to upload again to use the same video.
			</p>
			<p>
			<h3>How can I add multiple music tracks to my video?</h3>
			Select multiple tracks from the available music.  Drop the music that you want to add to the timeline one after the other.  Trim and move music on the timeline as desired.
			</p>
			<p>
			<h3>Is there a limit to number of music tracks that I can sync with the video?</h3>
			No.
			</p>
			<p>
			<h3>Can I save the video with selected music?</h3>
			No.  You can only preview the video with the selected music.  To save video with selected music, download music and use your own tools to save music to the video.
			</p>
			<p>
			<h3>How can I trim the music?</h3>
			Select the music by click on it on the timeline.  Use ‘Trim’ tool on the player.  Another way is to move the mouse to either end of the music.  The cursor changes to ‘Trim’ cursor.  Simply click and drag to trim as desired.
			</p>
			<p>
			<h3>How can I trim the video?</h3>
			Yes
			</p>
			<p>
			<h3>How can I remove a video or music from the Timeline window?</h3>
			Select the video or music by clicking on it.  Once highlighted, click on ‘Delete Selected Media’ button to delete it.
			</p>
			<p>
			<h3>How can I scale the timeline?</h3>
			Click and drag the scale slider at the bottom of the timeline window as desired.
			</p>
			<p>
			<h3>Can I upload my own audio?</h3>
			No.
			</p>			

		</div>
	</div>
</div> <!-- Content Ends -->
	{include file=$oView->getTemplateFile('momusicfooter','/shared') pageTitle="momusic"}

