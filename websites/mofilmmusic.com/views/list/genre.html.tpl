{include file=$oView->getTemplateFile('momusicheader','/shared') pageTitle="momusic"}
	{include file=$oView->getTemplateFile('momusicsidebar','/shared') pageTitle="momusic"}
	<div style="width:740px;float:right;  height:inherit;">
		<div>
			{assign var="cnt" value=1}
			{if $id}
				<div style="background-image:url(/themes/momusic/images/title_back_main.gif);">
					<div style="background-image:url(/themes/momusic/images/title_back_gm.gif);height:35px;padding-top:10px;float:left;padding-left:5px;">
						<strong><a href="/list/genre">Genre - </a></strong>{$oModel->getParentName($id)}</strong>
					</div>
					<div style="float:left;"> 
						<img src="/themes/momusic/images/arrow.gif" />
					</div>	
					<div style="clear:both;"> </div>
				</div>
				<div style="height:8px;"></div>
				{foreach $oList as $oObject}
					{if $cnt == 1}
						<div  class="moodraw3"><a href="/list/sub/{$oObject->getID()}">{$oObject->getName()}</a></div>
						<div  class="moodraw_spacer2">&nbsp;</div>		
						{math assign="cnt" equation="$cnt+1"}
					{else if $cnt == 2}
						<div  class="moodraw2" ><a href="/list/sub/{$oObject->getID()}">{$oObject->getName()}</a></div>
						<div  class="moodraw_spacer">&nbsp;</div>
						{math assign="cnt" equation="$cnt+1"}
					{else}
						<div class="moodraw1"><a href="/list/sub/{$oObject->getID()}">{$oObject->getName()} </a></div>
						<div  class="moodraw_spacer">&nbsp;</div>
						{assign var="cnt" value=1}
					{/if}		
				{/foreach}	

			{else}
				{foreach $oList as $oObject}
					{if $cnt == 1}
						<div  class="moodraw3"><a href="/list/genre/{$oObject->getID()}">{$oObject->getName()}</a></div>
						<div  class="moodraw_spacer2">&nbsp;</div>
						{math assign="cnt" equation="$cnt+1"}
					{else if $cnt == 2}
						<div class="moodraw2"><a href="/list/genre/{$oObject->getID()}">{$oObject->getName()} </a></div>
						<div  class="moodraw_spacer">&nbsp;</div>						
						{math assign="cnt" equation="$cnt+1"}
					{else}
						<div  class="moodraw1" ><a href="/list/genre/{$oObject->getID()}">{$oObject->getName()}</a></div>
						<div  class="moodraw_spacer">&nbsp;</div>						
						{assign var="cnt" value=1}
					{/if}		
				{/foreach}	

			{/if}
		</div>
    </div>
</div> <!-- Content Ends -->

{include file=$oView->getTemplateFile('momusicfooter','/shared')}