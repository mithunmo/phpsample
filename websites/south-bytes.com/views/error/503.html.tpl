{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}503 - Server Not Available{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<div>
				<h2>{t}Server Not Available{/t}</h2>
				<p>{t}The server is temporarily not available.{/t}</p>
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}