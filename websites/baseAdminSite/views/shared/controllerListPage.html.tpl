{include file=$oView->getTemplateFile('header', 'shared') pageTitle=$oMap->getDescription()}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<div class="floatLeft sideBar">
				{if !$parentController}{assign var=parentController value='home'}{/if}
				{generatePath controller=$oMap->getUriPath() parent=$parentController}
			</div>

			<div class="floatLeft main">
				{include file=$oView->getTemplateFile('controllerList', '/shared')}
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}