{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Dashboard{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared') nocache}

	<div id="content">
		{include file=$oView->getTemplateFile('statusMessage', '/shared') nocache}

		{$oView->getControllerView('eventManager', '/admin/eventadmin/eventManager', 'events', 'collapse=true&maxAge=1') nocache}
	</div>

{include file=$oView->getTemplateFile('footer', 'shared') nocache}