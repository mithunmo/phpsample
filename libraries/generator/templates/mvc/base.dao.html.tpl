{ldelim}include file=$oView->getTemplateFile('header','/shared') pageTitle="{$controllerName}"{rdelim}

		<h1>{$controllerName}</h1>
		<p>This is the standard view for {$controllerClass}. You will now have to customise it along with the controller, model and view.</p>

{ldelim}include file=$oView->getTemplateFile('footer','/shared'){rdelim}