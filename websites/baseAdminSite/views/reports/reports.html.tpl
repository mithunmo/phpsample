{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Reports{/t}'|cat:' - '|cat:$view|ucwords}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<div class="floatLeft sideBar">
				{include file=$oView->getTemplateFile('reportMenu')}
			</div>

			<div class="floatLeft main">
				{include file=$oView->getTemplateFile($view)}
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}