{include file=$oView->getTemplateFile('header','/shared') pageTitle="MOFILM Top Filmmakers by Country"}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body" class="whale">
		<div class="container">
			<div id="profilelanding">
				{*<div class="beta"><img src="{$themeimages}/profile/beta.png" alt="beta" /></div>*}
				<div class="header">{strip}
					<span>
						{if is_object($oTerritory) && $oTerritory->getID() > 0}
							{t}Top Filmmakers in {$oTerritory->getCountry()|ucwords|truncate:30:''}{/t}
						{else}
							{t}Top Filmmakers by Country{/t}
						{/if}
					</span>
				{/strip} </div>

				<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
					{include file=$oView->getTemplateFile('menu') selected='country'}
					
					<div class="ui-tabs-panel ui-widget-content ui-corner-bottom">
						<div class="leaderboard">
							{if is_object($oTerritory) && $oTerritory->getID() > 0}
								<div id="back">
									<a href="/user/country" title="{t}Back to Country List{/t}"><img src="{$themeimages}/icons/32x32/action-back.png" alt="back" class="icon" /> Back</a>
								</div>
								{include file=$oView->getTemplateFile('leaderboard') linkType='country' linkPage=$oTerritory->getShortName() highscore=true}
							{else}
								<ul>
								{foreach $countries as $oCountry}
									<li><img src="/themes/shared/flags/{$oCountry->getShortName()|lower}.png" alt="{$oCountry->getShortName()}" /> <a href="/user/country/{$oCountry->getShortName()|lower}" title="{t}Click to view the top filmmakers in {$oCountry->getCountry()}{/t}">{$oCountry->getCountry()}</a></li>
								{/foreach}
								</ul>
							{/if}

							<div class="clearBoth"></div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="clearBoth"></div>
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared') footerClass='whale'}
