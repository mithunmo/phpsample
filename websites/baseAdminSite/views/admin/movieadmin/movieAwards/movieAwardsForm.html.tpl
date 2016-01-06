{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Movie ID{/t}</th>
				<td><input type="text" name="MovieID" value="{$oObject->getMovieID()}" class="integer" /></td>
			</tr>
			<tr>
				<th>{t}Event{/t}</th>
				<td>{eventSelect id="eventList" name='EventID' selected=$oObject->getEventID() class="valignMiddle string" user=$oUser}</td>
			</tr>
			<tr>
				<th>{t}Source{/t}</th>
				<td>
					{if $oObject->getSourceID()}
						{sourceSelect id="eventListSources" name='SourceID' selected=$oObject->getSourceID() eventID=$oObject->getEventID() class="valignMiddle string" user=$oUser}
					{else}
						<select id="eventListSources" name="SourceID" size="1" class="valignMiddle string"><option>{t}Select event{/t}</option></select>
					{/if}
				</td>
			</tr>
			<tr>
				<th>{t}Position{/t}</th>
				<td>
					<select name="Position" size="1">
						<option value="">{t}Not Applicable{/t}</option>
						{for $i=1 to 10}
						<option value="{$i}" {if $oObject->getPosition() == $i}selected="selected"{/if}>{$i}</option>
						{/for}
					</select>
				</td>
			</tr>
			<tr>
				<th>{t}Type{/t}</th>
				<td>
					<select name="Type" size="1">
						{foreach mofilmMovieAward::getTypes() as $type}
						<option value="{$type}" {if $oObject->getType() == $type}selected="selected"{/if}>{$type}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<th>{t}Name{/t}</th>
				<td><input type="text" name="Name" value="{$oObject->getName()}" class="long" /></td>
			</tr>
			<tr>
				<th>{t}Year{/t}</th>
				<td><input type="text" name="Year" value="{$oObject->getYear()}" class="integer" /></td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getName()}&quot;?{/t}</p>
{/if}