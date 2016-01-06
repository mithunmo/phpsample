{include file=$oView->getTemplateFile('header','/shared') pageTitle="MOFILM CREW BUILDER"}
{include file=$oView->getTemplateFile('menu', 'shared')}

<div id="body" class="whale">
	<div class="container">
		<div id="profilelanding">
			<div class="header">{strip}
				<span>
					MOFILM CREW BUILDER - Search
				</span>
				{/strip} </div>


				<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
					
					<div style="color:#000000;font-size: 18px;padding-bottom: 10px;padding-left: 10px;padding-top:10px;"> Specify the City </div>
					<div style="height:50px;padding-left:20px;">
						<input type="text" id="location" size="40px">
					</div>	
					
					<div style="color:#000000;font-size: 18px;padding-bottom: 10px;padding-top:3px;padding-left: 10px;"> Select a desired Skill</div>
					<div class="ui-tabs-panel ui-widget-content ui-corner-bottom">
						<div class="leaderboard" style="padding-top:10px;">
							<div>
								{foreach $roles as $oRole}
									<div class="round"> 
										{$oRole->getDescription()}
									</div>

								{/foreach}	
							</div>
							<div class="clearBoth"></div>
						</div>
					</div>

					<div style="height:80px;padding-left:20px;">
						<div id="searchskill" class="roundsearch">Search </div>
					</div>	
					
					<div style="height:180px;padding-left:10px;">
					<h3>About Crew builder</h3>
					<p>
					MOFILM Crew Builder is a free tool to help MOFILMers find talented crew members for their next MOFILM video contest project. 
					Just specify a particular skill and the city you need it in and our Crew Builder tool will do the rest, giving you a list of every relevant MOFILMer in that location. 
					You can check out the profiles from your search results and then send a private message directly to the MOFILMers you're interested in working with. 
					They will be notified of your interest and can respond via private message to you. If you wish, you can then exchange your contact information over the private message platform to help recruit the member for your next project.			</p>
					<p>
					Interested in becoming part of MOFILM Talent Community ? Please <a href="/account/register"> <strong>Register</strong> </a> yourself and create your profile for free.
					</p>
					</div>	
				</div>
			</div>

			<div class="clearBoth"></div>
		</div>
	</div>

	{include file=$oView->getTemplateFile('footer', 'shared') footerClass='whale'}
