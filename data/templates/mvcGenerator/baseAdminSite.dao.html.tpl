{ldelim}include file=$oView->getTemplateFile('header', 'shared') pageTitle='{ldelim}t{rdelim}{$controllerName}{ldelim}/t{rdelim}'{rdelim}
{ldelim}include file=$oView->getTemplateFile('menu', 'shared'){rdelim}

	<div id="body">
		<div class="container">
			{ldelim}include file=$oView->getTemplateFile('statusMessage', '/shared'){rdelim}

			<div class="floatLeft sideBar">
				<h3>{ldelim}t{rdelim}Side Bar{ldelim}/t{rdelim}</h3>
			</div>

			<div class="floatLeft main">
				<h2>{ldelim}t{rdelim}{$controllerName}{ldelim}/t{rdelim}</h2>
				<p>This is the standard view for {$controllerClass}. You will now have to customise it along with the controller, model and view.</p>
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{ldelim}include file=$oView->getTemplateFile('footer', 'shared'){rdelim}