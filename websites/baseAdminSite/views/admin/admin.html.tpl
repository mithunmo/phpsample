{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}MOFILM Admin Centre{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<div class="floatLeft sideBar alignCenter">
				<img src="{$themeicons}/96x96/admin.png" alt="Admin" class="largeIcon" />
				<h3>{t}MOFILM Admin Centre{/t}</h3>
			</div>

			<div class="floatLeft main">
				{include file=$oView->getTemplateFile('controllerList', '/shared')}
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}