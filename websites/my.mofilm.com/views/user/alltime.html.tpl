{include file=$oView->getTemplateFile('header','/shared') pageTitle="MOFILM All Time Top Filmmakers"}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body" class="whale">
		<div class="container">
			<div id="profilelanding">
				{*<div class="beta"><img src="{$themeimages}/profile/beta.png" alt="beta" /></div>*}
				<div class="header"><span>{t}Top Filmmakers of All Time{/t}</span></div>

				<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
					{include file=$oView->getTemplateFile('menu') selected='alltime'}
					
					<div class="ui-tabs-panel ui-widget-content ui-corner-bottom">
						<div class="leaderboard">
							{include file=$oView->getTemplateFile('leaderboard') linkType='alltime' linkPage='page' highscore=2}

							<div class="clearBoth"></div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="clearBoth"></div>
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared') footerClass='whale'}