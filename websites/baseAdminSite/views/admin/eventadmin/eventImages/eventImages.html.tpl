{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}eventImages{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<div class="floatLeft sideBar">
				{if !isset($parentController)}{assign var=parentController value='admin'}{/if}
				{generatePath controller=$oMap->getUriPath() parent=$parentController}
			</div>

			<div class="floatLeft main">
				<h2>{t}Event Image Manager{/t}</h2>
				<p>{t}Select the event to change the images for.{/t}</p>

				{foreach $events as $oEvent}
					<div class="floatLeft spacer">
						<div>
							<a href="{$editURI}/{$oEvent->getID()}" title="{t}Change this events images{/t}">
								<img src="{$clientEventFolder}/{$oEvent->getLogoName()}.jpg" alt="event" style="width: 150px; height: 78px; border:  1px solid #000;" />
							</a>
						</div>
						<div>
							<em>{$oEvent->getName()}</em>
						</div>
					</div>
				{/foreach}

				<br class="clearBoth" />
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}