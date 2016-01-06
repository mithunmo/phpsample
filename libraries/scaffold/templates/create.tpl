{include file='header.tpl'}

<div id="scaffoldBody">
	{include file='formHeader.tpl'}
	<form class="scaffoldForm" name="create" action="{$smarty.server.SCRIPT_NAME}" method="post">
		<table cellpadding="2" cellspacing="0" border="0">
		{foreach name=loop key=propertyName item=propertyValue from=$propertyValues}
			{assign var=method value=$propertyName|replace:"_":"get"}
			{if !is_object($DaoObject->$method()) && !is_array($DaoObject->$method())}
			<tr>
				<td class="propertyLabel {if $smarty.foreach.loop.iteration % 2 == 0}alt{/if}">{$propertyName|replace:'_':''}</td>
				<td class="propertyInput {if $smarty.foreach.loop.iteration % 2 == 0}alt{/if}"><input type="text" name="{$propertyName}" value="{$propertyValue}" /></td>
			</tr>
			{/if}
		{/foreach}
			<tr>
				<td colspan="2" class="scaffoldActions">
					<input type="submit" name="action" value="retrieve" class="scaffoldActionRetrieve" />
					<input type="reset" name="reset" value="reset" class="scaffoldActionReset" />
					<input type="submit" name="action" value="docreate" class="scaffoldActionCreate" />
				</td>
			</tr>
		</table>
	</form>
</div>

{include file='footer.tpl'}