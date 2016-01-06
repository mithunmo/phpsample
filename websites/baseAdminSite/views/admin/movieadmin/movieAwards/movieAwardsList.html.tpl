{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=limit value=20}
{assign var=eventID value=$oController->getSearchParameter('EventID')}
{assign var=type value=$oController->getSearchParameter('Type')}
{assign var=objects value=$oModel->getObjectList($offset, $limit, $eventID, $type)}
{assign var=totalObjects value=$oModel->getTotalObjects()}

<div class="filters">
	{eventSelect name='EventID' selected=$eventID class="valignMiddle string" user=$oUser}
	
	<select name="Type" size="1">
		<option value="">{t}Any Type{/t}</option>
		{foreach mofilmMovieAward::getTypes() as $atype}
		<option value="{$atype}" {if $type == $atype}selected="selected"{/if}>{$atype}</option>
		{/foreach}
	</select>
</div>

{if $objects->getArrayCount() > 0}
	<table class="data">
		<thead>
			<tr>
				<th class="first"></th>
				<th>{t}Movie{/t}</th>
				<th>{t}Event{/t}</th>
				<th>{t}Source{/t}</th>
				<th>#</th>
				<th>{t}Type{/t}</th>
				<th>{t}Name{/t}</th>
				<th class="last">&nbsp;</th>
			</tr>
		</thead>
		<tfoot>
			{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=6}
		</tfoot>
		<tbody>
		{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=6}
		{foreach $objects as $oObject}
			<tr class="{cycle values="alt,"}">
				<td>{$oObject@iteration+$offset}</td>
				<td><a href="{adminMovieLink movieID=$oObject->getMovieID()}" title="{t}See movie{/t}">{$oObject->getMovieID()}</a></td>
				<td>{if $oObject->getEventID()}{$oObject->getEvent()->getName()|xmlstring}{else}--{/if}</td>
				<td>{if $oObject->getSourceID()}{$oObject->getSource()->getName()|xmlstring}{else}--{/if}</td>
				<td>{$oObject->getPosition()}</td>
				<td>{$oObject->getType()}</td>
				<td>{$oObject->getName()|xmlstring}</td>
				<td class="actions">
					{include file=$oView->getTemplateFile('daoObjectListDefaultActions', '/shared')}
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
{else}
	<p>{t}No objects found in system.{/t}</p>
{/if}
