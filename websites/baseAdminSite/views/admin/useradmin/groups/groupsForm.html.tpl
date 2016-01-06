{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Group Name{/t}</th>
				<td><input type="text" name="Description" value="{$oObject->getDescription()}" class="string" /></td>
			</tr>
			
			{if $oController->getAction() == 'newObject'}
			<tr>
				<th>{t}Copy permissions from{/t}</th>
				<td>
					<select name="CopyPermissions" size="1" class="string">
						<option value="">{t}don't copy{/t}</option>
						<option value="">--</option>
						{foreach $groups as $oGroup}
						<option value="{$oGroup->getID()}">{$oGroup->getDescription()}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			{/if}
			
			<tr>
				<th>{t}Basic Admin Access{/t}</th>
				<td>
					<input type="checkbox" name="GrantBasePermissions" value="1" /><br />
					<em>{t}Assigns view and edit permissions to the group for all admin components.{/t}</em>
				</td>
			</tr>
		</tbody>
	</table>
	
	<p>
		{t}Set group permissions by placing a tick in the boxes below.{/t}
		{t}To select a range of checkboxes: select the first and then hold down the SHIFT key.{/t}
		{t}Click the second checkbox and all in-between will be checked or unchecked.{/t}
	</p>
	
	<table class="data">
		<thead>
			<tr>
				<th colspan="2">{t}General Permissions{/t}</th>
			</tr>
		</thead>
		<tbody>
			{foreach $permissions as $oPermission}
				{include file=$oView->getTemplateFile('permissions') level=0}
			{/foreach}
		</tbody>
	</table>
	
	{foreach $oControllerMap->getMapAsControllers() as $oMapCtrl}
		<br />
		
		<table class="data">
			<thead>
				<tr>
					<th colspan="2">{$oMapCtrl->getDescription()} {t}Permissions{/t}</th>
				</tr>
			</thead>
			<tbody>
			{include file=$oView->getTemplateFile('controller') oMapCtrl=$oMapCtrl level=0}
			</tbody>
		</table>
	{/foreach}
	
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getDescription()}&quot;?{/t}</p>
{/if}