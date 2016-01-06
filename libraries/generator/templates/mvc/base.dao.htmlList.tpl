{ldelim}assign var=offset value=$pagingOffset|default:0{rdelim}
{ldelim}assign var=limit value=30{rdelim}
{ldelim}assign var=totalObjects value=$oModel->getTotalObjects(){rdelim}
{ldelim}assign var=objects value=$oModel->getObjectList($offset, $limit){rdelim}
{ldelim}if $objects->getArrayCount() > 0{rdelim}
	<table class="data">
		<thead>
			<tr>
{foreach $daoObjectVars as $value}
				<th>{$value|replace:"_":""|spacifyCapitalisedString}</th>
{/foreach}
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		{ldelim}include file=$oView->getTemplateFile('daoPaging', '/shared') colspan={$daoObjectVars->getArrayCount()-1}{rdelim}
		{ldelim}foreach $objects as $oObject{rdelim}
			<tr>
{foreach $daoObjectVars as $value}
				<td {ldelim}if $oObject@iteration % 2{rdelim}class="alt"{ldelim}/if{rdelim}>{ldelim}$oObject->get{$value|replace:"_":""}(){rdelim}</td>
{/foreach}
				<td class="actions {ldelim}if $oObject@iteration % 2{rdelim}alt{ldelim}/if{rdelim}">
					{ldelim}include file=$oView->getTemplateFile('daoObjectListDefaultActions', '/shared'){rdelim}
				</td>
			</tr>
		{ldelim}/foreach{rdelim}
		{ldelim}include file=$oView->getTemplateFile('daoPaging', '/shared') colspan={$daoObjectVars->getArrayCount()-1}{rdelim}
		</tbody>
	</table>
{ldelim}else{rdelim}
	<p>No objects found in system.</p>
{ldelim}/if{rdelim}
