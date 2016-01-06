{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Code{/t}</th>
				<td><input type="text" name="Code" value="{$oObject->getCode()}" /></td>
			</tr>
			<tr>
				<th>{t}Description{/t}</th>
				<td><input type="text" name="Description" value="{$oObject->getDescription()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Location{/t}</th>
				<td><input type="text" name="Location" value="{$oObject->getLocation()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Territory{/t}</th>
				<td>{territorySelect selected=$oObject->getTerritoryID() name='TerritoryID'}</td>
			</tr>
			<tr>
				<th>{t}Start Date{/t}</th>
				<td>
					<input type="text" name="StartDate" value="{$oObject->getStartDate()|date_format:'%Y-%m-%d'}" class="date datepicker" />
					<strong>@</strong>
					{html_select_time field_array='StartdateTime' prefix='' display_seconds=false minute_interval=10 time=$oObject->getStartDate()}
				</td>
			</tr>
			<tr>
				<th>{t}End Date{/t}</th>
				<td>
					<input type="text" name="EndDate" value="{$oObject->getEndDate()|date_format:'%Y-%m-%d'}" class="date datepicker" />
					<strong>@</strong>
					{html_select_time field_array='EnddateTime' prefix='' display_seconds=false minute_interval=10 time=$oObject->getEndDate()}
				</td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getDescription()}&quot;?{/t}</p>
{/if}