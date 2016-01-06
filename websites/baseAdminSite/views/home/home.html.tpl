{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Dashboard{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared') nocache}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared') nocache}

			<div class="floatLeft sideBar">
				{nocache}
					{$oView->getControllerView('videos', '/videos', 'movieStats') nocache}
					<hr />
					{if $oController->hasAuthority('canSeeUserStats')}
						{$oView->getControllerView('users', '/users', 'userStats') nocache}
						<hr />
					{/if}
					
					{if $oUser->getPermissions()->isRoot()}
						{include file=$oView->getTemplateFile('changeUser', '/users') nocache}
						<hr />
					{/if}
				{/nocache}
			</div>

			<div class="floatLeft main">
				<h2>{t}Dashboard{/t}</h2>
				{$oView->getControllerView('motd', '/admin/other/motd', 'motd') nocache}
				
				{$oView->getControllerView('eventManager', '/admin/eventadmin/eventManager', 'events', 'collapse=true&maxAge=1') nocache}
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared') nocache}