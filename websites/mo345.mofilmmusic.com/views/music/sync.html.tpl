{include file=$oView->getTemplateFile('momusicsyncheader','/shared') pageTitle="momusic"}
<script type="text/javascript">

	function myfunc(id) {
	 jQuery(document).ready(function(){

		$( '<div id="dialog" title="Basic dialog">Your work is being saved.You will be redirected to the saved link.Please copy the link from the address bar to share.<form> <fieldset> <label for="name">Project Name</label> <input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" /> </fieldset> </form>	</div>' ).dialog({
					title: "Confirmation",
					modal: true,
					buttons: {
						"Save": function() {
							var name = $("#name").val();
							
							if ( name.length <= 2  ) {
							
							 $("#name").addClass( "ui-state-error" );
							} else {
								$("#name").removeClass( "ui-state-error" );
								window.location="/music/saveWork?name="+$("#name").val()+"&id="+id;
							}
					},
					Cancel: function() {
						$( this ).dialog( "close" );
					}											
					}

		});
	});
	
	//
	}			
	

	
</script>	

{assign var=offset value=$oResults->getSearchInterface()->getStart()|default:0}
{assign var=limit value=20}
{assign var=totalObjects value=$oResults->getTotalResults()}

<!-- Content Starts --> 
<div style="height:auto;">
	<div style="width:740px;float:left;">

		<div id="moviemasher_container">	
			<strong>You need to upgrade your Flash Plugin to version 10 and enable JavaScript</strong>
		</div>

		<div style="height:10px;"></div>	

		<div style="width:1030px;text-align:center;height:47px;background-image:url(/themes/momusic/images/backimage_editor.gif);">
			
			<div style="float:right;padding-right:400px;width:384px;"> 
			
				{include file=$oView->getTemplateFile('audioplayer','/shared') pageTitle="momusic"}			
			</div>
			
			<div style="float:left;padding-left:50px;padding-top: 5px;"> 
			
				{if $oLogged}
					<input type="file" id="myfile" name="myfile" style="cursor:pointer;">
				{else}
					<input id="uploadlogin" class="uploadbutton" style="width:150px;" type="button" value="Upload Video">
				{/if}
			
			</div>			

			
		</div>	

		<div style="height:10px;"></div>	

			{if $totalObjects > 0 }
			<div id="navlink" style="font-size:10px;width:1030px;"> 
				<div style="float:left;width:450px; ">

					{if ($offset-$limit) >= 0}
						<a style="cursor: pointer;" id="prevSearch1" class="searchsync"  title="{t}Previous{/t}"><img src="/themes/mofilm/images/icons/22x22/prev.png"></a>
						{else}
						<a style="cursor: pointer;" title="{t}Previous{/t}"><img src="/themes/mofilm/images/icons/22x22/prev.png"></a>
						{/if}

				</div>

				{if $totalObjects > $limit}		
				<span style="width:100%;align:center;" id="totalb">Viewing 1-20 of {$totalObjects} Results</span>
				{else}
				<span style="width:100%;align:center;" id="totalb">Viewing 1-{$totalObjects} of {$totalObjects} Results</span>	
				{/if}
						
				<div style="float:right;width:250px;padding-right:5px;text-align:right;">
					{if $offset+$limit < $totalObjects}
						<a style="cursor: pointer;" id="nextSearch1" class="searchsync"  title="{t}Next{/t}"><img src="/themes/mofilm/images/icons/22x22/next.png"></a>
					{else}
						<a style="cursor: pointer;" title="{t}Next{/t}"><img src="/themes/mofilm/images/icons/22x22/next.png"></a>					
					{/if}


				</div>
			</div>
			{else}
				No results found
			{/if}	
		<input type="hidden" name="Offset" id="off" value=0>
				<div style="clear:both;"> </div>

			<div id="musicContent" style="height:auto; background-color:#f0efef;font-size:10px;width:1030px;">
				<table width="1030px;" height="50" border="0" align="center" cellpadding="0" cellspacing="3">
					{foreach $oResults as $oMusic}						
						<tr>
							<td width="20px"><a class="musicPlay" href="{$oMusic->s_id}"><img style="padding-right:10px;" src="/themes/mofilm/images/mm/play.png" height="16px;"></a> </td>
							<td width="150px">{$oMusic->s_track}<br />{$oMusic->s_artist}</td>										
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
	
		<div style="height:10px;"></div>	
		
		
	</div>	
</div> <!-- Content Ends -->
</div></div>

{include file=$oView->getTemplateFile('momusicfooter','/shared') pageTitle="momusic"}
