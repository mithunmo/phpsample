{include file=$oView->getTemplateFile('momusicheader','/shared') pageTitle="momusic"}
{include file=$oView->getTemplateFile('momusicsidebar','/shared') pageTitle="momusic"}
	<div style="width:740px;float:right;  height:inherit;">
		<div style="height:8px;"></div>
		<div>
					<div  class="moodraw3"><a href="/music/solrSearch/?keyword=fast tempo">Fast Tempo</a></div>
					<div  class="moodraw_spacer2">&nbsp;</div>
					
					<div  class="moodraw2" ><a href="/music/solrSearch/?keyword=slow tempo">Slow Tempo</a></div>
					<div  class="moodraw_spacer">&nbsp;</div>
					
					<div class="moodraw1"><a href="/music/solrSearch/?keyword=medium tempo">Medium Tempo</a></div>
					<div  class="moodraw_spacer">&nbsp;</div>
					
					<div class="moodraw3"><a href="/music/solrSearch/?keyword=medium fast tempo">Medium fast Tempo</a></div>
					<div  class="moodraw_spacer2">&nbsp;</div>
					
					<div class="moodraw2"><a href="/music/solrSearch/?keyword=medium slow tempo">Medium slow Tempo</a></div>
					<div  class="moodraw_spacer">&nbsp;</div>			
					
		</div>
    </div>
</div> <!-- Content Ends -->

{include file=$oView->getTemplateFile('momusicfooter','/shared')}