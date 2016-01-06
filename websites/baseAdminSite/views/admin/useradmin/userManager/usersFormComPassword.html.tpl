{if $oController->hasAuthority('admin.userManagerController.canChangePassword')}
	<tr>
		<th>{if $oController->getAction() == 'newObject'}{t}Password{/t}{else}{t}Change Password{/t}{/if}</th>
		<td><input type="password" name="Password" value="" /></td>
	</tr>
{/if}