{include file=$oView->getTemplateFile('header','/shared') pageTitle="MOFILM - Home"}
{include file=$oView->getTemplateFile('menu','/shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<h1>terms</h1>
			<p>This is the standard view for termsController. You will now have to customise it along with the controller, model and view.</p>

		</div>
	</div>

{include file=$oView->getTemplateFile('footer','/shared')}