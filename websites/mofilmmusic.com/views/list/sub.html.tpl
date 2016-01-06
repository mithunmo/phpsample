{include file=$oView->getTemplateFile('momusicheader','/shared') pageTitle="momusic"}
	{include file=$oView->getTemplateFile('momusicsidebar','/shared') pageTitle="momusic"}
	<div style="width:740px;float:right;  height:inherit;">
		<div style="height:8px;"></div>
		<div>
			{if $id}
				<div><strong>{$oFamily->getTypeName()} / {$oModel->getFamilyName($oFamily->getID())}</strong></div>
				<div style="height:8px;"></div>				
			{/if}	
			{assign var="cnt" value=1}
			{foreach $oList as $oObject}
				{if $cnt == 1}
					<div  class="moodraw3"><a href="/list/sub/{$oObject->getID()}">{$oObject->getName()}</a></div>
					<div  class="moodraw_spacer"></div>
					{math assign="cnt" equation="$cnt+1"}					
				{else if $cnt == 2}
					<div  class="moodraw2" ><a href="/list/sub/{$oObject->getID()}">{$oObject->getName()}</a></div>
					<div  class="moodraw_spacer"></div>
					{math assign="cnt" equation="$cnt+1"}					
				{else}
					<div class="moodraw1"><a href="/list/sub/{$oObject->getID()}">{$oObject->getName()} </a></div>
					<div  class="moodraw_spacer"></div>
					{assign var="cnt" value=1}
				{/if}		
			{/foreach}	
		</div>
    </div>
</div> <!-- Content Ends -->

{include file=$oView->getTemplateFile('momusicfooter','/shared')}







