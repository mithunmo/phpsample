{if $oController->hasAuthority('admin.userManagerController.canChangeClient')}
	<tr>
		<th>{t}Client{/t}</th>
		<td>{clientSelect name='ClientID' class='string' selected=$oObject->getClientID()}</td>
	</tr>
{/if}