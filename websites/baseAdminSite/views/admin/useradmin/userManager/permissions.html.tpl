{if $oPermission->getName() != 'root'}
<tr>
	<td class="level{$level}"><abbr title="{$oPermission->getDescription()|escape:'htmlall':'UTF-8'}">{$oPermission->getName()}</abbr></td>
	<td class="alignCenter last">
		<input type="checkbox" class="shiftCheckEnable" name="Permissions[]" value="{$oPermission->getName()}" {if $oObject->getPermissions()->isUserPermission($oPermission->getName())}checked="checked"{/if}/>
	</td>
</tr>
{/if}