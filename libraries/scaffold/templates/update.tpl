{include file='header.tpl'}

<div id="scaffoldBody">
	{include file='formHeader.tpl'}
	
	<form class="scaffoldForm" name="update" action="{$smarty.server.SCRIPT_NAME}" method="post">
		<table cellpadding="2" cellspacing="0" border="0">
		{foreach name=loop item=property from=$properties}
			{assign var=method value=$property|replace:"_":"get"}
			{if !is_object($DaoObject->$method()) && !is_array($DaoObject->$method())}
			<tr>
				<td class="propertyLabel {if $smarty.foreach.loop.iteration % 2 == 0}alt{/if}">{$property|replace:'_':''}</td>
				<td class="propertyInput {if $smarty.foreach.loop.iteration % 2 == 0}alt{/if}"><input type="text" name="{$property}" value="{$DaoObject->$method()}" /></td>
			</tr>
			{/if}
		{/foreach}
			<tr>
				<td colspan="2" class="scaffoldActions">
					<input type="submit" name="action" value="retrieve" class="scaffoldActionRetrieve" />
					<input type="reset" name="reset" value="reset" class="scaffoldActionReset" />
					<input type="submit" name="action" value="doupdate" class="scaffoldActionUpdate" />
				</td>
			</tr>
		</table>
	</form>
</div>

{include file='footer.tpl'}