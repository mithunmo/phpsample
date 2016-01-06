{include file=$oView->getTemplateFile('header','/shared') pageTitle="MOFILM CREW BUILDER"}
{include file=$oView->getTemplateFile('menu', 'shared')}

<div id="body" class="whale">
	<div class="container">
		<div id="profilelanding">
			<div class="header"><span>{t}Mofilm Crew Builder - Results{/t}</span></div>
			
			<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all"> 
				<div style="width:200px; display:inline-block;height:40px;text-align:center;float:left;" >
					<a href="/user/crew"><img src="{$themeimages}/icons/32x32/action-back.png" alt="back to Search" class="icon" /> Back to Search</a>
				</div>
				<div style="width:330px; display:inline-block;height:40px;text-align:center;float:left; padding-top:4px;" >
					<strong>{$total} profiles found , Showing Page {$page} of {$lastPage}</strong>
				</div>	 
				<div style="width:230px;display:inline-block;height:40px;text-align:right;float:right; padding-top:4px; padding-right:20px; background-repeat:no-repeat; background-image:url('/themes/mofilm/images/search.gif')" >
					<input style="height:17px; width:165px; border-style:none; border-color:white; outline:none; " type="text; font-size:10px;" id="ckeyword" name="userSearch" placeholder="Search by City, Skill" value="{$key}">
				</div> 
				<div style="height:30px"> </div>
	
				<div class="ui-tabs-panel ui-widget-content ui-corner-bottom">
					<div class="skills">
						{include file=$oView->getTemplateFile('crewbuilder') linkType='alltime' linkPage='page' highscore=2}
						<div class="clearBoth"></div>
					</div>
				</div>
			</div>
		</div>

		<div class="clearBoth"></div>
	</div>
</div>

{include file=$oView->getTemplateFile('footer', 'shared') footerClass='whale'}