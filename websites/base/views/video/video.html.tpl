{include file=$oView->getTemplateFile('header','/shared') pageTitle="MOFILM - Video - "|cat:$oMovie->getShortDesc()|xmlstring description=$oMovie->getDescription()|xmlstring}
		
	<div class="movieplayer">
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

{include file=$oView->getTemplateFile('footer','/shared')}