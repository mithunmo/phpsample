{include file=$oView->getTemplateFile('musicheader','/shared') pageTitle="momusic"}
{include file=$oView->getTemplateFile('musicmenu','/shared')}
	<div id="body">
		<div class="container">

		<h1>{t}Download your track...{/t}</h1>
		
		<div class="downloadContainer">
			<div class="image"><img src="/themes/mofilm/images/license.png" border="0" alt="Your download is ready"></div>
			
			<div class="details">
				<h2>{t}Music Details{/t}</h2>
				{*include file=$oView->getTemplateFile('fileInfo', 'download')*}
					<div class="fileDetails">
						<div class="row">
							<div class="name">{t}Filename:{/t}</div>
							<!--div class="value"><a href="/download/movie?url={$oObject->getPath()}">Download Track</a></div-->
							<div class="value">{$oObject->getTrackName()}</div>
						</div>
						<div class="row">
							<div class="name">{t}Description:{/t}</div>
							<div class="value">{$oObject->getDescription()}</div>
					</div>				
						<div class="row">
							<div class="name">{t}Download :{/t}</div>
							<div class="value"><a href="/music/download/{$oObject->getID()}">Download Track</a></div>
			</div>
					</div>				
			</div>

			<div class="clearBoth"></div>
		</div>
		</div>
	</div>				
				
		
{include file=$oView->getTemplateFile('footer','/shared')}