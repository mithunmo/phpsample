{if $oController->hasAuthority('admin.userManagerController.canChangeStatus')}
	<tr>
		<th>{t}Enabled{/t}</th>
		<td>{yesNoSelect name='Enabled' class='short' selected=$oObject->getEnabled()}</td>
	</tr>
{/if}