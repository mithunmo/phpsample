{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Imap Server{/t}</th>
				<td><input type="text" name="ImapServer" value="{$oObject->getImapServer()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Imap Port{/t}</th>
				<td><input type="text" name="ImapPort" value="{$oObject->getImapPort()}" class="small" /></td>
			</tr>
			<tr>
				<th>{t}Imap Folder{/t}</th>
				<td><input type="text" name="ImapFolder" value="{$oObject->getImapFolder()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Daemon Email{/t}</th>
				<td><input type="text" name="DaemonEmail" value="{$oObject->getDaemonEmail()}" class="long" /></td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record ID &quot;{$oController->getPrimaryKey()}&quot;?{/t}</p>
{/if}