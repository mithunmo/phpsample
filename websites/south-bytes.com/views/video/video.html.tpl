{include file=$oView->getTemplateFile('header','/shared') pageTitle="SOUTHBYTES the official video voice of SXSW"|cat:$oMovie->getShortDesc()|xmlstring description=$oMovie->getDescription()|xmlstring}
{include file=$oView->getTemplateFile('menu','/shared') selected="gallery"}


	<div id="sbcontentleft">
		<div class="sbcontent">
			<div class="movieplayer">
				<h2 class="movieTitle">{$oMovie->getShortDesc()|xmlstring}</h2>
				<h4 class="movieUser">uploaded by {$oMovie->getCreditText()|xmlstring}</h4>

				<div id="mofilmMoviePlayer">
					<p>{t}THIS SITE REQUIRES THE ADOBE FLASH PLAYER{/t}</p>
					<p>{t}CLICK THE IMAGE BELOW TO DOWNLOAD{/t}</p>
					<p>{strip}
						<a href="http://www.adobe.com/go/getflashplayer" title="{t}Get Adobe Flash Player{/t}">
							<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="{t}Get Adobe Flash player{/t}" border="0" />
						</a>
					{/strip}</p>
				</div>
			</div>

			<div class="clear" /></div>
			
			<p><a href="/gallery">See more movies</a></p>
		</div>
	</div>

	<div id="sbcontentright">
		
	</div>



{include file=$oView->getTemplateFile('footer','/shared')}