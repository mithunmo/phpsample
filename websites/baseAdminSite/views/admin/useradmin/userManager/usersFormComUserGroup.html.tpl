{if $oController->hasAuthority('root') || ($oController->hasAuthority('admin.userManagerController.canChangeGroup') && !$oObject->getPermissions()->isRoot())}
	<tr>
		<th>{t}Permissions Group{/t}</th>
		<td>{permissionGroupSelect name='PermissionGroupID' class='' selected=$oObject->getPermissionGroup()->getID()}</td>
	</tr>
{/if}