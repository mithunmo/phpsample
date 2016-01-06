{assign var=industryid value=$brandDetails['industryid']}
<h3><a href="#">{t}Tags{/t}</a></h3>
<div id="tabs">
	<ul>
		{*<li><a href="#tagsTab">User Tags</a></li>*}
		{*<li><a href="#genresTab">Genres</a></li>*}
		<li><a href="#categoriesTab">Categories</a></li>
		<li><a href="#newGenresTab">Genres</a></li>
	</ul>
    
	{*<div id="tagsTab" class="floatLeft spacer">
				{foreach $tags as $oTag}
						<div class="adminTags" id="adminTags">
						{$oTag->getName()}
						<!--<a id="deleteTag" href="#{$oTag->getID()}">
							<image src="/themes/mofilm/images/delete.gif" alt="Close" height="12" width="12" align="right">
						</a>-->
						<input type="hidden" name="Tags[]" value="{$oTag->getID()}" />
						</div>
				{/foreach}
	</div>*}

	{*<div id="genresTab" class="floatLeft spacer">
		{foreach $genres as $oTag}
			<div class="adminTags" id="adminGenres">
			    {$oTag->getName()}
			    <a href="#{$oTag->getID()}">
				<image src="/themes/mofilm/images/delete.gif" alt="Close" height="12" width="12">
			    </a>
			</div>
		{/foreach}
	</div>*}

	<div id="categoriesTab" class="floatLeft spacer">
		{foreach $categories as $oTag}
			<div class="adminTags" id="adminCategories">
				{$oTag->getName()}
			</div>
		{/foreach}
	</div>
	
	<div id="newGenresTab" class="floatLeft spacer">
		{assign var=i value=1}
                {assign var=pfcount value=0}
                {assign var=sbcount1 value=0}
                {assign var=sbcount2 value=0}
                {foreach $newGenres as $oTags}
		    <div style="width:570px;  padding:4px;">
                        {if $oTags@key == "Product Focus"}
                          <strong>Type of film*</strong>  
                          <div style="margin: 10px 0 5px;float: left;width: 100%;"><strong>Commercials and one-offs</strong> </div>
                         {else}   
                        <strong>{$oTags@key}</strong>
                        {/if}
                        </div>
			<div style="padding:4px;width:570px; overflow:auto;">
				{foreach $oTags as $oTag}
					<div style="float:left; width:185px;">
                                                
						{if $oTag->getCategory() == "Industry"}
                                                    {if $industryid==$oTag->getID()}
                                                    <input class="industry" type="checkbox" name="Indtags[]" value="{$oTag->getID()}" {if $industryid==$oTag->getID()}checked{/if} style="float: left;" /><span style="float: left;width: 160px;">{$oTag->getName()}</span>
                                                    {/if}
						{else}
                                                {if $oTag->getCategory() == "Product Focus"}    
                                                   {$pfcount = $pfcount+1} 
                                                    {if $pfcount > 4}
                                                     {if $sbcount1 == 0}
                                                     </div> <div style='width:570px; overflow:auto;'><div style="margin: 10px 0 5px;float: left;width: 100%;"><strong>Episodic content</strong></div>
                                                {$sbcount1 = $sbcount1+1}
                                                
                                                {/if}
                                            
                                            {else}
                                            
                                                {if $sbcount2 == 0}
                                                {assign var=sbcount2 value=1} 
                                                {/if}
                                             
                                            {/if}
                                              {else}
                                             {/if}       
                                                    
                                                    
                                             <input type="checkbox" name="Tags[]" value="{$oTag->getID()}" {if $oMovie->getTagSet()->hasTag($oTag->getName())}checked{/if} style="float: left;"  /><span style="float: left;width: 160px;">{$oTag->getName()}</span>
						{/if}
                                                
					</div>
				{/foreach}
			</div>
		{/foreach}
	</div>
	
	<br class="clearBoth" />
</div>