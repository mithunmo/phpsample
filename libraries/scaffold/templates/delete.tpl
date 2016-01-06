{include file='header.tpl'}

<div id="scaffoldBody">
	{include file='formHeader.tpl'}
	<form class="scaffoldForm" name="delete" action="{$smarty.server.SCRIPT_NAME}" method="post">
		<p>Are you sure you wish to delete this record with values:</p>
		<table cellpadding="2" cellspacing="0" border="0">
		{foreach name=loop item=property from=$properties}
			{assign var=method value=$property|replace:"_":"get"}
			{if !is_object($DaoObject->$method()) && !is_array($DaoObject->$method())}
			<tr>
				<td class="propertyLabel {if $smarty.foreach.loop.iteration % 2 == 0}alt{/if}">{$property|replace:'_':''}</td>
				<td class="propertyInput {if $smarty.foreach.loop.iteration % 2 == 0}alt{/if}"><input type="hidden" name="{$property}" value="{$DaoObject->$method()}" />{$DaoObject->$method()}</td>
			</tr>
			{/if}
		{/foreach}
			<tr>
				<td class="scaffoldActions" colspan="2">
					<input type="submit" name="action" value="retrieve" class="scaffoldActionRetrieve" />
					<input type="submit" name="action" value="dodelete" class="scaffoldActionDelete" />
				</td>
			</tr>
		</table>
	</form>
</div>

{include file='footer.tpl'}