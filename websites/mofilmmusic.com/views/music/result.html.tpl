{include file=$oView->getTemplateFile('momusicheader','/shared') pageTitle="momusic"}
{assign var=offset value=$oResults->getSearchInterface()->getStart()|default:1}
{assign var=limit value=20}
{assign var=totalObjects value=$oResults->getTotalResults()}

<!-- Content Starts --> 
	{include file=$oView->getTemplateFile('momusicsidebarresult','/shared') pageTitle="momusic"}
	<div style="width:740px;float:right;">
		<div>

			<div style="width:640px;text-align:center;height:47px;padding-left:180px;background-image:url(/themes/momusic/images/player_back.jpg);">
				{include file=$oView->getTemplateFile('audioplayer','/shared') pageTitle="momusic"}
			</div>	
			
			<div style="height:10px;"></div>	
			{if $search}
				<div style="height:30px;font-size:12px;">Music Listings for 
					<strong><div class="adminTags">{$search} </div>
					{if $oModel->getFilter()} 
						{assign var=arr value=$oModel->getNewUrl()}

						{foreach $arr as $key => $val}
						<div class="adminTags">
						<a href="{$req}?{$val}">{$key}
							<img height=12 width=12 src="/themes/mofilm/images/delete.gif"> 
						</a>
						</div>	
						{/foreach}
					{/if}</strong>
				</div>
				<div style="height:10px;"></div>	
			{/if}
			
			{if $totalObjects > 0 }
			<div id="navlink" style="font-size:10px;"> 
				<div style="float:left;width:300px;">
					
					{if ($offset-$limit) >= 0}
						<a style="cursor: pointer;" id="prevSearch1" class="searchmomusic"  title="{t}Previous{/t}"><img src="/themes/mofilm/images/icons/22x22/prev.png"></a>
					{else}
						<a style="cursor: pointer;" title="{t}Previous{/t}" ><img src="/themes/mofilm/images/icons/22x22/prev.png"></a>
					{/if}

				</div>
				{assign var='o' value=$offset + 1}	
				{if $totalObjects > $limit}		
				{assign var='l' value=$offset+$limit}	
				
				<span id="totalb">Viewing {$o} - {$l} of {$totalObjects} Results</span>
				{else}
				<span id="totalb">Viewing {$o} -{$totalObjects} of {$totalObjects} Results</span>	
				{/if}

				<div style="float:right;width:250px;padding-right:5px;text-align:right;">
					{if $offset+$limit < $totalObjects}
						<a style="cursor: pointer;" id="nextSearch1" class="searchmomusic"  title="{t}Next{/t}"><img src="/themes/mofilm/images/icons/22x22/next.png"></a>
					{else}
						<a style="cursor: pointer;" title="{t}Next{/t}"><img src="/themes/mofilm/images/icons/22x22/next.png"></a>			
					{/if}


				</div>
			</div>
			{else}
				No results found
			{/if}		

			<input type="hidden" name="Offset" id="off" value={$offset}>

			<div style="clear:both;"> </div>

			<div id="musicContent" style="height:auto; background-color:#f0efef;font-size:10px;">
				<table width="740" height="50" border="0" align="center" cellpadding="0" cellspacing="3">
					{foreach $oResults as $oMusic}						
						<tr>
							<td width="20px"><a rel="nofollow" class="musicPlay" href="{$oMusic->s_id}"><img style="padding-right:10px;" src="/themes/mofilm/images/mm/play.png" height="16px;"></a> </td>
							<td width="150px">{$oMusic->s_track}<br /> <a href="/music/solrSearch?artist={urlencode($oMusic->s_artist)}"><strong>{$oMusic->s_artist}</strong></a></td>
                                                        <td width="420px">{$oMusic->s_description}</td>        
							<td style="text-align:right;" width="40px">{$oMusic->s_duration}</td>
							<td width="40px" align="center" valign="middle"><a class="cart" style="color:black;" href="{$oMusic->s_id}"><img src="/themes/mofilm/images/cart.gif"></a></td>
						</tr>
						<tr>
							<td height="1px"  style="background-color:#fff"colspan="5"></td>
						</tr>						
					{/foreach}	
				</table> 			
			</div>
			{*foreach $oFacet as $mainKey => $oFacetValue}
				{$mainKey}
				{foreach $oFacetValue as $key => $item}
					<a href="{$currentUrl}&category={$mainKey}&filterq={$key}">{$key}({$item})</a>
				{/foreach}	
				<br />
			{/foreach*}
			<div style="height:10px;"></div>	
		</div>
	</div>	
</div> <!-- Content Ends -->

{include file=$oView->getTemplateFile('momusicfooter','/shared') pageTitle="momusic"}
