{if $oController->hasAuthority('root') || $oUser->getClientID() == mofilmClient::MOFILM}
	<tr>
		<th>{t}Registered{/t}</th>
		<td>{$oObject->getRegistered()}</td>
	</tr>
	<tr>
		<th>{t}Registered from IP{/t}</th>
		<td>{$oObject->getRegIP()|default:'{t}Unknown{/t}'}</td>
	</tr>
	<tr>
		<th>{t}Hash{/t}</th>
		<td>{$oObject->getHash()}</td>
	</tr>
{/if}