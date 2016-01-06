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
				<form id="adminFormData" name="formData" method="{$formMethod}" class="{if $formMonitor}monitor{/if}" action="{$formAction}" accept-charset="utf-8" {if $formEncType}enctype="{$formEncType}"{/if}>
					<h2>{$oMap->getDescription()}{if $daoUriAction != 'viewObjects' && $oObject && $oObject->getPrimaryKey()} - {$daoUriAction|replace:'Object':''|capitalize} - Object #{$oObject->getPrimaryKey()}{/if}</h2>
					<div class="content">
						<div id="adminActions" class="body">
							{include file=$oView->getActionsMenuView()}
						</div>
						<div class="clearBoth"></div>
					</div>

					<div class="content">
						<div class="body">
							{if $daoUriAction == 'viewObjects'}
								{include file=$oView->getObjectListView()}
							{else}
								{include file=$oView->getObjectFormView()}
							{/if}
						</div>
						<div class="clearBoth"></div>
					</div>
				</form>
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}