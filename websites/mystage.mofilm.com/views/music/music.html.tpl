{include file=$oView->getTemplateFile('musicheader','/shared') pageTitle="momusic"}
{include file=$oView->getTemplateFile('musicmenu','/shared')}


{assign var=offset value=$oResults->getSearchInterface()->getOffset()|default:0}
{assign var=limit value=$oResults->getSearchInterface()->getLimit()}
{assign var=totalObjects value=$oResults->getTotalResults()}


{*

{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=20}
{assign var=objects value=$oModel->getObjectList($offset, $limit)}
{assign var=totalObjects value=$oModel->getTotalObjects()}

*}


<div id="body" style="background-color:#dcdcdc;">

	<div class="container">
		<!--script type="text/javascript" src="http://mediaplayer.yahoo.com/js"></script--> 			
		<!--script type="text/javascript" src="http://webplayer.yahooapis.com/player.js"></script--> 
		{include file=$oView->getTemplateFile('statusMessage', '/shared')}
		<div id="moviemasher_container" style="background-color:#dcdcdc;">	
			<strong>You need to upgrade your Flash Plugin to version 10 and enable JavaScript</strong>
		</div>

		<div style="width:900px; height:30px; background-image:url(/themes/mofilm/images/mm/upload/backimage.gif); background-repeat:no-repeat; padding-top:8px; padding-bottom:9px;">
			<div style="float:left; width:130px; padding-left:10px;">
						{if $oLogged}
						<input type="file" id="myfile" name="myfile"> &nbsp; <br />
						{else}
							<input id="uploadlogin" type="submit" value="Upload Video" style=" width:100px; height:33px; ">
						{/if}
			</div>
			<div style=" float:left; height:30px; width:120px;"><input id="clear" type="button" value="Clear Workspace" style=" width:120px; height:33px; background-color:#dcdcdc;border-left:white;  "></div>
			<form action="/music/search" method="GET">
			<div style="float:right; width:35px; padding-right:25px; padding-left:20px; "><input class="searchGo" id="nSearch" type="submit" value="GO !" style=" width:52px; height:33px; "><!--img src="/themes/mofilm/images/mm/catre/go_btn.gif" width="52" height="33" alt="search" /--></div>
			<div style="float:right; width:220px; padding-right:5px; padding-left:20px;"><input style="border-bottom: 1px solid white; border-top: 1px solid white; border-left: 1px solid white; border-right: 1px solid white; padding-left: 10px;" id="search" name="keyword" type="text" value=" " placeholder="Search for genres, artists"size="29" maxlength="200" /></div>
			</form>
		</div>
		<div style="clear:both; height:1px;"></div>					
		<div id="totalc" style="color:#0780ba;font-weight: bold;" align="center"></div>
		<div class="bordersecondrow">	
			<div class="textproperties1" style="color:#0780ba;"><strong>License</strong></div>
			<div style="float:right; width:2px; background-repeat:repeat-x;"></div>
			<div class="textproperties2" style="color:#0780ba;"><strong>Duration</strong></div>
			<div style="float:right; width:2px; background-repeat:repeat-x;"></div>
			<div class="textproperties3" style="color:#0780ba;"><strong>Artist Name</strong></div>
			<div style="float:right; width:2px; background-repeat:repeat-x;"></div>
			<div class="textproperties4" style="color:#0780ba;"><div><strong>Sync</strong></div></div>
			<div style="float:right; width:2px; background-repeat:repeat-x;"></div>
			<div class="textproperties5" style="color:#0780ba;"><strong>Track Name</strong></div>
		</div><!-- row 1 Ends-->
		<!--div style=" height: 1px;"></div-->
		
		<div id="musicContent">
		{assign var="cnt" value=1}
		{foreach $oResults as $oMusic}		
			{math assign="res" equation="$cnt%2"}
			{if $res == 0}
				<div class="bordersecondrow">
			{else if}
				<div class="bordersecondrow">	
			{/if}
				<div class="textproperties1"><a style="color:black;" href="/music/license/{$oMusic->getID()}"><img src="/themes/mofilm/images/cart.gif"></a></div>
				<div style="float:right; width:2px; background-repeat:repeat-x;"></div>
				<div class="textproperties2">{$oMusic->getDuration()}</div>
				<div style="float:right; width:2px; background-repeat:repeat-x;"></div>
				<div class="textproperties3">{$oMusic->getArtistID()}</div>
				<div style="float:right; width:2px; background-repeat:repeat-x;"></div>
				<div class="textproperties4"><div class="addAudio"><input type="checkbox" /></div></div>
				<div style="float:right; width:2px; background-repeat:repeat-x;"></div>
				<div class="textproperties5"> <p> <a class="sm2_button" style="margin-bottom:5px; color:black;" href="{$oMusic->getPath()}"> {$oMusic->getTrackName()} </a> {$oMusic->getTrackName()}<p></div>
			</div><!-- row 1 Ends-->
			<!--div style=" height: 1px;"></div-->
			{math assign="cnt" equation="$cnt+1"}
		{/foreach}

		<div style="width:50px;float:left;">

		{if ($offset-$limit) >= 0}
		<a id="prevSearch" class="searchGo" href="{$daoUriView}?Offset={$offset-$limit}&amp;Limit={$limit}{if $daoSearchQuery}&amp;{$daoSearchQuery}{/if}" title="{t}Previous{/t}"><img src="/themes/mofilm/images/icons/32x32/result-set-previous.png"></a>		
		{/if}
		</div>
		
		<div style="width:50px;float:right;padding-right: 20px;">

		{if $offset+$limit < $totalObjects}
		<a id="nextSearch" class="searchGo" href="{$daoUriView}?Offset={$offset+$limit}&amp;Limit={$limit}{if $daoSearchQuery}&amp;{$daoSearchQuery}{/if}" title="{t}Next{/t}"><img src="/themes/mofilm/images/icons/32x32/result-set-next.png"></a>
		{/if}
		</div>			
		
		<div style="clear:both;"> </div>

		</div>
		<input type="hidden" name="Offset" id="off" value=0>

	</div>		
</div>
{include file=$oView->getTemplateFile('footer','/shared')}
