{include file='header.tpl'}

<div id="scaffoldBody">
	{include file='formHeader.tpl'}
	<form class="scaffoldForm" name="create" action="{$smarty.server.SCRIPT_NAME}" method="post"><p><input type="submit" name="action" value="create" class="scaffoldActionCreate" /> a new {$ObjectName}.</p></form>
	<table cellpadding="2" cellspacing="0" border="0" class="scaffoldTable">
		<thead>
			{foreach item=property from=$properties}
			<td><strong>{$property|replace:"_":""}</strong></td>
			{/foreach}
			<td><strong>Tools</strong></td>
		</thead>
		<tbody>
		{foreach name=records item=DaoObject from=$records}
			<form name="retrieve{$smarty.foreach.records.iteration}" action="{$smarty.server.SCRIPT_NAME}" method="post">
				<tr>
				{foreach item=property from=$properties}
					<td class="{if $smarty.foreach.records.iteration % 2 == 0}alt{/if}">
					{assign var=method value=$property|replace:"_":"get"}
					{if !is_object($DaoObject->$method()) && !is_array($DaoObject->$method())}
						<input type="hidden" name="{$property}" value="{$DaoObject->$method()}" /> {$DaoObject->$method()|default:"&nbsp;"}
					{else}
						<em>Result is array or object</em>
					{/if}
					</td>
				{/foreach}
					<td class="scaffoldActions {if $smarty.foreach.records.iteration % 2 == 0}alt{/if}">
						<input type="submit" name="action" value="update" class="scaffoldActionUpdate" />
						<input type="submit" name="action" value="delete" class="scaffoldActionDelete" />
					</td>
				</tr>
			</form>
		{foreachelse}
			<td colspan="{$propertyCount+1}">No records found</td>
		{/foreach}
		</tbody>
	</table>
</div>

{include file='footer.tpl'}