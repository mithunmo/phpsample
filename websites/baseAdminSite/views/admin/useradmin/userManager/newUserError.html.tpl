{include file=$oView->getTemplateFile('header', 'shared') pageTitle=$oMap->getDescription()}
{include file=$oView->getTemplateFile('menu', 'shared')}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}

			<div class="floatLeft sideBar">
				{if !isset($parentController)}{assign var=parentController value='admin'}{/if}
				{generatePath controller=$oMap->getUriPath() parent=$parentController}
			</div>

			<div class="floatLeft main">
				<h2>{t}Oops - User Exists{/t}</h2>
				<div class="content">
					<div id="adminActions" class="body">
						{include file=$oView->getActionsMenuView()}
					</div>
					<div class="clearBoth"></div>
				</div>

				<div class="content">
					<div class="body">
						<p>{t}The username / email address you supplied is already registered for admin access.{/t}</p>
						{if $oUser->getPermissions()->isRoot() || ($oUser->getClientID() == mofilmClient::MOFILM && $oController->hasAuthority('userManagerController.edit'))}
							<p>{t}You can edit this record to update the details: <a href="{$daoUriEdit}/{$oModel->getID()}">Edit User</a>{/t}</p>
						{else}
							<p>{t}Please contact MOFILM Support to have the user details updated.{/t}</p>
						{/if}
						<p><a href="{$daoUriView}">{t}Back to User Manager{/t}</a></p>
					</div>
					<div class="clearBoth"></div>
				</div>
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}