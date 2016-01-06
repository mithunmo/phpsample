{include file=$oView->getTemplateFile('momusicheader','/shared') pageTitle="momusic"}

{include file=$oView->getTemplateFile('momusicsidebar','/shared') pageTitle="momusic"}
	<div style="width:740px;float:right;  height:inherit;">
		<div style="height:8px;"></div>
		<div>
{if $id}
	{assign var="cnt" value=1}
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
       {if $catalog == "mozayic"}
 	{assign var="cnt" value=1}
	{foreach $oList as $oObject}
		{if $cnt == 1}
					<div  class="moodraw3"><a href="/list/mood/2/{$oObject}">{$oObject}</a></div>
					<div  class="moodraw_spacer2">&nbsp;</div>
					{math assign="cnt" equation="$cnt+1"}
		{else if $cnt == 2}
					<div  class="moodraw2" ><a href="/list/mood/2/{$oObject}">{$oObject}</a></div>
					<div  class="moodraw_spacer">&nbsp;</div>
					{math assign="cnt" equation="$cnt+1"}
		{else}
					<div class="moodraw1"><a href="/list/mood/2/{$oObject}">{$oObject}</a></div>
					<div  class="moodraw_spacer">&nbsp;</div>
					{assign var="cnt" value=1}
		{/if}		
	{/foreach}	
          
           
       {else}    
	{assign var="cnt" value=1}
	{foreach $oList as $oObject}
		{if $cnt == 1}
					<div  class="moodraw3"><a href="/list/mood/{$oObject->getID()}">{$oObject->getName()}</a></div>
					<div  class="moodraw_spacer2">&nbsp;</div>
					{math assign="cnt" equation="$cnt+1"}
		{else if $cnt == 2}
					<div  class="moodraw2" ><a href="/list/mood/{$oObject->getID()}">{$oObject->getName()}</a></div>
					<div  class="moodraw_spacer">&nbsp;</div>
					{math assign="cnt" equation="$cnt+1"}
		{else}
					<div class="moodraw1"><a href="/list/mood/{$oObject->getID()}">{$oObject->getName()}</a></div>
					<div  class="moodraw_spacer">&nbsp;</div>
					{assign var="cnt" value=1}
		{/if}		
	{/foreach}	
       {/if} 

{/if}
		</div>
    </div>
</div> <!-- Content Ends -->

{include file=$oView->getTemplateFile('momusicfooter','/shared')}