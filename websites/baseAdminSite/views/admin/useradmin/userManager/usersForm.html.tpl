{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden">
		<input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" />
		<input type="hidden" name="Registered" value="{$oObject->getRegistered()}" />
		<input type="hidden" name="RegIP" value="{$oObject->getRegIP()}" />
		<input type="hidden" name="Hash" value="{$oObject->getHash()}" />
	</div>
	<div id="userFormAccordion">
		
		<h3><a href="#">{t}User Details{/t}</a></h3>
		<div>
			<table class="data">
				<tbody>
					{if $oController->getAction() == 'newObject'}
						{include file=$oView->getTemplateFile('usersFormComEmail')}
						{include file=$oView->getTemplateFile('usersFormComName')}
						{include file=$oView->getTemplateFile('usersFormComUserGroup')}
						{include file=$oView->getTemplateFile('usersFormComClient')}
					{else}
						{include file=$oView->getTemplateFile('usersFormComEmail')}
						{include file=$oView->getTemplateFile('usersFormComPassword')}
						{include file=$oView->getTemplateFile('usersFormComName')}
						{include file=$oView->getTemplateFile('usersFormComUserGroup')}
						{include file=$oView->getTemplateFile('usersFormComClient')}
						{include file=$oView->getTemplateFile('usersFormComTerritory')}
						{include file=$oView->getTemplateFile('usersFormComStatus')}
						{include file=$oView->getTemplateFile('usersFormComExtra')}
					{/if}
				</tbody>
			</table>
		</div>
		
		{if $oController->hasAuthority('admin.userManagerController.canEditUserProperties')}
		<h3><a href="#">{t}Custom Properties{/t}</a></h3>
		<div>
			<table class="data">
				<tbody>
					{foreach $properties as $propName => $displayName}
						<tr class="{cycle values="alt,"}">
							<td>{$displayName}</td>
							<td class="alignLeft">
								<input type="textbox" id="property{$propName}" name="Properties[{$propName}]" value="{$oObject->getParamSet()->getParam($propName)}" class="{if $propName == 'DateOfBirth'}date{else}string{/if}" />
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
		{/if}
		
		{if $oController->hasAuthority('admin.userManagerController.canEditUserPermissions')}
		<h3><a href="#">{t}Custom Permissions{/t}</a></h3>
		<div>
			<p>
				{t}Set group permissions by placing a tick in the boxes below.{/t}
				{t}To select a range of checkboxes: select the first and then hold down the SHIFT key.{/t}
				{t}Click the second checkbox and all in-between will be checked or unchecked.{/t}
			</p>
			<table class="data">
				<thead>
					<tr>
						<th colspan="2">{t}General Permissions{/t}</th>
					</tr>
				</thead>
				<tbody>
					{foreach $permissions as $oPermission}
						{include file=$oView->getTemplateFile('permissions') level=0}
					{/foreach}
				</tbody>
			</table>
			
			{foreach $oControllerMap->getMapAsControllers() as $oMapCtrl}
				<br />
				
				<table class="data">
					<thead>
						<tr>
							<th colspan="2">{$oMapCtrl->getDescription()} {t}Permissions{/t}</th>
						</tr>
					</thead>
					<tbody>
					{include file=$oView->getTemplateFile('controller') oMapCtrl=$oMapCtrl level=0}
					</tbody>
				</table>
			{/foreach}
		</div>
		{/if}
	</div>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record ID &quot;{$oController->getPrimaryKey()}&quot;?{/t}</p>
{/if}