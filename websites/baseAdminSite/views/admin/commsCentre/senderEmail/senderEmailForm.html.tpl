{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Name{/t}</th>
				<td><input type="text" name="Name" value="{$oObject->getName()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Sender Email{/t}</th>
				<td><input type="text" name="SenderEmail" value="{$oObject->getSenderEmail()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Sender Password{/t}</th>
				<td><input type="password" name="SenderPassword" value="{$oObject->getSenderPassword()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Select the IMAP server{/t}</th>
				<td>
				    <select name="ImapServer" value="{$oObject->getImapServerID()}" class="long">
					{foreach $imapList as $fileall}
					    <option value="{$fileall->getID()}"> {$fileall->getImapServer()} </option>
					{/foreach}
				    </select>
				</td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getName()}&quot;?{/t}</p>
{/if}