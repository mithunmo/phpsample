{assign var=permissions value=mofilmPermission::getControllerPermissions($oMapCtrl->getName())}
{foreach $permissions as $oPermission}
	{include file=$oView->getTemplateFile('permissions') level=$level}
{/foreach}
{if $oMapCtrl->hasSubControllers()}
	{foreach $oMapCtrl->getSubControllers() as $oSubMapCtrl}
		<tr>
			<td colspan="2" class="level{$level} permissionTitle">{$oSubMapCtrl->getDescription()} Permissions</td>
		</tr>
		{include file=$oView->getTemplateFile('controller') oMapCtrl=$oSubMapCtrl level=$level+1}
	{/foreach}
{/if}