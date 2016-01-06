<!-- left bar Starts -->
<div class="left_block">
<!--
	<div style="background-color:#FFF;">
		<div class="gradient3"><div class="sidebartitle"><strong>Browse by Category</strong></div></div>
	</div>
    
    <div>
		<div class="cbdb-menu" style="cursor:pointer;" onclick="location.href='/list/style'">
			<a class="blue">Musical Style</a>
			<div class="arrow_icon"><img src="/themes/momusic/images/icon1.gif"/></div>
		</div>
		<div class="cbdb-menu" style="cursor:pointer;" onclick="location.href='/list/mood'">
			<a class="blue">Mood / Emotions</a>
			<div class="arrow_icon"><img src="/themes/momusic/images/icon1.gif"/></div>
		</div>
		<div class="cbdb-menu" style="cursor:pointer;" onclick="location.href='/list/instrument'">
			<a class="blue">Instruments</a>
			<div class="arrow_icon"><img src="/themes/momusic/images/icon1.gif"/></div>
		</div>
		<div class="cbdb-menu" style="cursor:pointer;" onclick="location.href='/list/genre'">
			<a class="blue">Genre</a>
			<div class="arrow_icon"><img src="/themes/momusic/images/icon1.gif"/></div>
		</div>
		<div class="cbdb-menu" style="cursor:pointer;" onclick="location.href='/list/tempo'">
			<a class="blue">Tempo</a>
			<div class="arrow_icon"><img src="/themes/momusic/images/icon1.gif"/></div>
		</div>		
		
    </div>
-->
    <!--Left Menu Ends-->
	<div style="clear:both;"> </div>
	<div style="background-color:#FFF;">
		<div class="gradient3"><div class="sidebartitle"><strong>Refine Results by</strong></div></div>
	</div>
	<div style="padding:5px;">
	<div>
		{foreach $oFacet as $mainKey => $oFacetValue}
				{if $oFacetValue|@count > 0 }
				<div class="gradientFacet"><a href="/list/{$mainKey|substr:2}">{$mainKey|substr:2|upper}</a> </div>
				<div style="padding-left:10px;width:250px;">  
				<div class="thisdiv">
				<ul style="list-style-type: none;">
				{foreach $oFacetValue as $key => $item}
					{if $oModel->getFilter()}
						{if !$oModel->getFilterDefined($key)}
							<li style="width:160px;line-height: 20px; "><a href="{$currentUrl}&category={$oModel->getCategory()},{$mainKey}&filterq={$oModel->getFilter()},{$key}">{ucfirst($key)} &nbsp;({$item})</a></li>
							{else}
							<li style="width:160px;line-height: 20px;color: grey; ">{ucfirst($key)} &nbsp;({$item})</li>								
						{/if}
					{else}	
					<li style="width:160px;line-height: 20px; "><a href="{$currentUrl}&category={$mainKey}&filterq={$key}">{ucfirst($key)}&nbsp;({$item})</a></li>
					{/if}
				{/foreach}	
				</ul>
				</div>
				</div>
				<br />
				{/if}
		{/foreach}	
	</div>	
	</div>
</div>	
<!-- left bar Ends here -->
