{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Methods{/t}</th>
				<td>{$oObject}</td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete &quot;{$oObject}&quot;?{/t}</p>
{/if}