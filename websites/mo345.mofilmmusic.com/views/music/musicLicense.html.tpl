{include file=$oView->getTemplateFile('momusicheader','/shared') pageTitle="momusic"}
<!-- Content Starts --> 
<div style="height:500px;">
	{include file=$oView->getTemplateFile('momusicsidebar','/shared') pageTitle="momusic"}
	<div style="width:740px;float:right;">
		<div><img src="/themes/momusic/images/sample_banner.png" /></div>
		<div style="height:8px;"></div>

		<div class="details">
			<h2>{t}Music Details{/t}</h2>
			{*include file=$oView->getTemplateFile('fileInfo', 'download')*}
			<div class="fileDetails">
				<div class="row">
					<div class="name">{t}Filename: {$oObject->getSongName()}{/t}</div>

				</div>	
				<div class="row">
					<div class="name">{t}Description: {$oObject->getDescription()} {/t}</div>
				</div>
				<div class="row">
					<div class="name">{t}Download :<a href="/music/download/{$oObject->getID()}">Download Track</a> {/t}</div>
				</div>
			</div>				
		</div>


	</div>	
</div> <!-- Content Ends -->
</div></div>

{include file=$oView->getTemplateFile('momusicfooter','/shared') pageTitle="momusic"}

