{if $oController->hasAuthority('admin.userManagerController.canChangeTerritory')}
	<tr>
		<th>{t}Territory{/t}</th>
		<td>{territorySelect name='TerritoryID' class='string' selected=$oObject->getTerritoryID()}</td>
	</tr>
{/if}