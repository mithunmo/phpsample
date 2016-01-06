{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}ID{/t}</th>
				<td><input type="text" name="ID" value="{$oObject->getID()}" /></td>
			</tr>
			<tr>
				<th>{t}Event ID{/t}</th>
				<td><input type="text" name="EventID" value="{$oObject->getEventID()}" /></td>
			</tr>
			<tr>
				<th>{t}Source ID{/t}</th>
				<td><input type="text" name="SourceID" value="{$oObject->getSourceID()}" /></td>
			</tr>
			<tr>
				<th>{t}User ID{/t}</th>
				<td><input type="text" name="UserID" value="{$oObject->getUserID()}" /></td>
			</tr>
			<tr>
				<th>{t}Movie ID{/t}</th>
				<td><input type="text" name="MovieID" value="{$oObject->getMovieID()}" /></td>
			</tr>
			<tr>
				<th>{t}Grant ID{/t}</th>
				<td><input type="text" name="GrantID" value="{$oObject->getGrantID()}" /></td>
			</tr>
			<tr>
				<th>{t}Payment Type{/t}</th>
				<td><input type="text" name="PaymentType" value="{$oObject->getPaymentType()}" /></td>
			</tr>
			<tr>
				<th>{t}Comments{/t}</th>
				<td><input type="text" name="Comments" value="{$oObject->getComments()}" /></td>
			</tr>
			<tr>
				<th>{t}Submitter ID{/t}</th>
				<td><input type="text" name="SubmitterID" value="{$oObject->getSubmitterID()}" /></td>
			</tr>
			<tr>
				<th>{t}Approver ID{/t}</th>
				<td><input type="text" name="ApproverID" value="{$oObject->getApproverID()}" /></td>
			</tr>
			<tr>
				<th>{t}Approver Comments{/t}</th>
				<td><input type="text" name="ApproverComments" value="{$oObject->getApproverComments()}" /></td>
			</tr>
			<tr>
				<th>{t}Amount Grant{/t}</th>
				<td><input type="text" name="AmountGrant" value="{$oObject->getAmountGrant()}" /></td>
			</tr>
			<tr>
				<th>{t}Amount Paid{/t}</th>
				<td><input type="text" name="AmountPaid" value="{$oObject->getAmountPaid()}" /></td>
			</tr>
			<tr>
				<th>{t}Status{/t}</th>
				<td><input type="text" name="Status" value="{$oObject->getStatus()}" /></td>
			</tr>
			<tr>
				<th>{t}Created{/t}</th>
				<td><input type="text" name="Created" value="{$oObject->getCreated()}" /></td>
			</tr>
			<tr>
				<th>{t}Payment Desc{/t}</th>
				<td><input type="text" name="PaymentDesc" value="{$oObject->getPaymentDesc()}" /></td>
			</tr>
			<tr>
				<th>{t}Account User{/t}</th>
				<td><input type="text" name="AccountUser" value="{$oObject->getAccountUser()}" /></td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record ID &quot;{$oController->getPrimaryKey()}&quot;?{/t}</p>
{/if}