{include file=$oView->getTemplateFile('momusicheader','/shared') pageTitle="momusic"}
<div style="height:1200px;">
{include file=$oView->getTemplateFile('momusicsidebar','/shared') pageTitle="momusic"}
	<div style="width:740px;float:right;  height:inherit;">
		<div style="height:8px;"></div>
		<div>
					<div  class="moodraw3"><a href="/music/solrSearch/?tempo=fast tempo">Fast Tempo</a></div>
					<div  class="moodraw_spacer2">&nbsp;</div>
					
					<div  class="moodraw2" ><a href="/music/solrSearch/?tempo=slow tempo">Slow Tempo</a></div>
					<div  class="moodraw_spacer">&nbsp;</div>
					
					<div class="moodraw1"><a href="/music/solrSearch/?tempo=medium tempo">Medium Tempo</a></div>
					<div  class="moodraw_spacer">&nbsp;</div>
					
					<div class="moodraw3"><a href="/music/solrSearch/?tempo=medium fast tempo">Medium fast Tempo</a></div>
					<div  class="moodraw_spacer2">&nbsp;</div>
					
					<div class="moodraw2"><a href="/music/solrSearch/?tempo=medium slow tempo">Medium slow Tempo</a></div>
					<div  class="moodraw_spacer">&nbsp;</div>			
					
		</div>
    </div>
</div> <!-- Content Ends -->
</div></div>

{include file=$oView->getTemplateFile('momusicfooter','/shared')}