{include file=$oView->getTemplateFile('musicheader','/shared') pageTitle="momusic"}
{include file=$oView->getTemplateFile('musicmenu','/shared')}
<script type="text/javascript">

	function myfunc(id) {
	 jQuery(document).ready(function(){

		$( '<div id="dialog" title="Basic dialog">Your work is being saved.You will be redirected to the saved link.Please copy the link from the address bar to share.<form> <fieldset> <label for="name">Project Name</label> <input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" /> </fieldset> </form>	</div>' ).dialog({
					title: "Confirmation",
					modal: true,
					buttons: {
						"Save": function() {
							var name = $("#name").val();
							
							if ( name.length <= 2  ) {
							
							 $("#name").addClass( "ui-state-error" );
							} else {
								$("#name").removeClass( "ui-state-error" );
								window.location="/music/saveWork?name="+$("#name").val()+"&id="+id;
							}
					},
					Cancel: function() {
						$( this ).dialog( "close" );
					}											
					}

		});
	});
	
	//
	}			
	

	
</script>	

{assign var=offset value=$oResults->getSearchInterface()->getOffset()|default:0}
{assign var=limit value=$oResults->getSearchInterface()->getLimit()}
{assign var=totalObjects value=$oResults->getTotalResults()}


<div id="body" style="background-color:#dcdcdc;">

	<div class="container">
		{include file=$oView->getTemplateFile('statusMessage', '/shared')}
		<div id="mashload"></div>
		<div id="moviemasher_container" style="background-color:#dcdcdc;">	
			<strong>You need to upgrade your Flash Plugin to version 10 and enable JavaScript</strong>
		</div>

		<div style="width:1000px; height:47px; background-image:url(/themes/mofilm/images/mm/upload/backimage.gif); background-repeat:no-repeat; ">

			<div style="float:left; width:130px; padding-left:10px; padding-top:7px;">
						{if $oLogged}
						<input type="file" id="myfile" name="myfile" style="cursor:pointer;"> &nbsp; <br />
						{else}
							<input id="uploadlogin" class="button" type="button" value="Upload Video" style="color:white; border-left:white; background-color:#5b5a5a; width:100px; height:33px; cursor:pointer;">
						{/if}
			</div>
			<!--div style=" float:left; height:30px; width:120px; margin-bottom:1em; padding-top:7px;"><input id="clear" class="button" type="button" value="Clear Workspace" style="width:120px; height:33px; background-color:#5b5a5a; border-left:white; color:white;cursor:pointer;"></div-->
			
			<form action="/music/search" method="GET">
				<div style="float:right; width:35px; padding-right:25px; padding-left:20px; padding-top:7px;"><input class="searchGo button" id="nSearch" type="submit" value="GO" style=" width:52px; height:33px; background-color: #5b5a5a;cursor:pointer;"><!--img src="/themes/mofilm/images/mm/catre/go_btn.gif" width="52" height="33" alt="search" /--></div>
			
				<div style="float:right; width:200px; padding-right:5px; padding-left:20px; padding-top:7px;"><input style="border-bottom: 0px solid white;border-top: 0px solid white; border-left: 0px solid white; border-right: 0px white; padding-left: 1px;" id="search" name="keyword" type="text" value="" placeholder="Search for genres, artists" size="29" maxlength="200" /></div>
			</form>
			<div style="float:left; width:10px;">&nbsp;</div>
			<div style="float:left; height:42px; width:382px;padding-top: 2px; background-image: url(/libraries/jplayer/musicplayer_back.png); background-repeat: no-repeat; padding-left: 15px;">
				<div id="jquery_jplayer_1" class="jp-jplayer"></div>

				<div id="jp_container_1" class="jp-audio">
					<div class="jp-type-single">
						<div class="jp-gui jp-interface">
							<ul class="jp-controls">
								<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
								<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
								<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
								<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
								<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
								<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
							</ul>
							<div class="jp-progress">
								<div class="jp-seek-bar">
									<div class="jp-play-bar"></div>
		</div>
							</div>
							<div class="jp-volume-bar">
								<div class="jp-volume-bar-value"></div>
							</div>
							<div class="jp-time-holder">
								<div class="jp-current-time"></div>
								<div class="jp-duration"></div>

								<!--ul class="jp-toggles">
									<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>
									<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>
								</ul-->
							</div>
						</div>
						<!--div class="jp-title">
							<ul>
								<li>Cro Magnon Man</li>
							</ul>
						</div-->
						<div class="jp-no-solution">
							<span>Update Required</span>
							To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
						</div>
					</div>

				</div>	
			</div>		
		</div>

		<div style="clear:both; height:1px;"></div>					
		<div id="totalc" style="color:#0780ba;font-weight: bold;" align="center"></div>
		<div style=" width:1000px; height:30px; vertical-align:middle; padding:1px; background-color:#4b4b4b">
			<div style="float:right; color:white; height:25px; padding-top:5px; width:80px; background-color:#161616; text-align:center">
			Download
			</div>
			<div style="float:right; width:1px;">&nbsp;</div>
			<div style="float:right; color:white; height:25px; padding-top:5px; width:90px; background-color:#161616;  text-align:center ">
			Duration
			</div>
			<div style="float:right; width:1px;">&nbsp;</div>
			<div style="float:right; color:white; height:25px; padding-top:5px; width:50px; background-color:#161616;  text-align:center ">
			Add
			</div>
			<div style="float:right; width:1px;">&nbsp;</div>
			<div style="float:right; color:white; height:25px; padding-top:5px; width:240px; background-color:#161616;  text-align:center ">
			Artist Name
			</div>

			<div style="float:right; width:1px;">&nbsp;</div>
			<div style="float:right; color:white; height:25px; padding-top:5px; width:59px; background-color:#161616;  text-align:center ">
				Desc
			</div>
			
			
			<div style="float:right; width:1px;">&nbsp;</div>
			<div style="float:left; color:white; height:25px; padding-top:5px; width:476px; background-color:#161616;  text-align:center ">
			Track Name
			</div>
		</div>
		
		
		<!--div style=" height: 1px;"></div-->
		
		<div id="musicContent">
		{assign var="cnt" value=1}
		{foreach $oResults as $oMusic}		
			{math assign="res" equation="$cnt%2"}
			{if $res == 0}
				<!--div class="bordersecondrow"-->
			{else if}
				<!--div class="bordersecondrow"-->	
			{/if}
			<div style=" width:1000px; height:34px; vertical-align:middle; padding:1px; background-color:#b8b7b7">
				<div style="float:right; height:26px; padding-top:8px; width:80px; background-color:#f2f2f2; text-align:center">
					<a style="color:black;" href="/music/license/{$oMusic->getID()}"><img src="/themes/mofilm/images/cart.gif"></a>
				</div>
				<div style="float:right; width:1px;">&nbsp;</div>
				<div style="float:right; height:26px; padding-top:8px; width:90px; background-color:#f2f2f2;  text-align:center ">{$oMusic->getDuration()}</div>
				<div style="float:right; width:1px;">&nbsp;</div>
				<div style="float:right; height:26px; padding-top:8px; width:50px; background-color:#f2f2f2;  text-align:center ">
					<div class="addAudio"><img src="/themes/mofilm/images/add.png" height=16px width=16px /></div>
				</div>
				<div style="float:right; width:1px;">&nbsp;</div>
				<div style="float:right; height:26px; padding-top:8px; width:240px; background-color:#f2f2f2;  text-align:center ">{$oMusic->getArtistID()}</div>

				<div style="float:right; width:1px;">&nbsp;</div>
				<div class="tooltips" style="float:right; height:26px; padding-top:8px; width:59px; background-color:#f2f2f2;  text-align:center ">
					<img src="/themes/mofilm/images/mm/info.png" width=16px height=16px  /> <span class=".tooltip-style2"> {$oMusic->getDescription()} </span>
				</div>
				
				<div style="float:right; width:1px;">&nbsp;</div>
					{* <div style="float:left; height:26px; padding-top:8px; width:466px; background-color:#f2f2f2;  text-align:left; padding-left: 10px"><a class="sm2_button" style="margin-bottom:5px; color:black;" href="{$oMusic->getPath()}"></a> {$oMusic->getTrackName()}</div> *}
					{* <div style="float:left; height:26px; padding-top:8px; width:466px; background-color:#f2f2f2;  text-align:left; padding-left: 10px"><object type="application/x-shockwave-flash" data="/libraries/dewplayer/dewplayer.swf?mp3={$oMusic->getPath()}&showtime=true" width="200" height="20" id="dewplayer"><param name="wmode" value="transparent" /><param name="movie" value="dewplayer.swf?mp3={$oMusic->getPath()}&showtime=true" /></object>{$oMusic->getTrackName()}</div> *}
					<div style="float:left; height:26px; padding-top:8px; width:466px; background-color:#f2f2f2;  text-align:left; padding-left: 10px"><a class="musicPlay" href="{$oMusic->getPath()}"><img style="padding-right:10px;" src="/themes/mofilm/images/mm/play.png" height="16px;"></a>{$oMusic->getTrackName()}</div>
			</div>
			
			{math assign="cnt" equation="$cnt+1"}
		{/foreach}

		<div style="width:50px;float:left;padding-left: 350px;">

		{if ($offset-$limit) >= 0}
		<a id="prevSearch" class="searchGo" href="{$daoUriView}?Offset={$offset-$limit}&amp;Limit={$limit}{if $daoSearchQuery}&amp;{$daoSearchQuery}{/if}" title="{t}Previous{/t}"><img src="/themes/mofilm/images/icons/22x22/prev.png"></a>
		{else}
		<a class="searchGo" href="" title="{t}Previous{/t}"><img src="/themes/mofilm/images/icons/22x22/prev.png"></a>
		{/if}
		</div>
		<span id="totalb"><strong>Viewing 1-20 of {$totalObjects} Results</strong></span>
		<div style="width:50px;float:right; padding-right: 350px;">
		
		{if $offset+$limit < $totalObjects}
		<a id="nextSearch" class="searchGo" href="{$daoUriView}?Offset={$offset+$limit}&amp;Limit={$limit}{if $daoSearchQuery}&amp;{$daoSearchQuery}{/if}" title="{t}Next{/t}"><img src="/themes/mofilm/images/icons/22x22/next.png"></a>
		{/if}
		</div>			
		
		<div style="clear:both;"> </div>

		</div>
		<input type="hidden" name="Offset" id="off" value=0>

				<form id="mashMusic" onsubmit="return evaluateExpression(this);">
					<br />
					<textarea id="expression" cols="70" rows="10" hidden="hidden">{$oObject->getXml()}</textarea>
					<br />
				</form>
		
	</div>		
</div>
<script>

</script>
{include file=$oView->getTemplateFile('footer','/shared')}
