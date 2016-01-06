{ldelim}assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0{rdelim}
{ldelim}assign var=limit value=20{rdelim}
{ldelim}assign var=objects value=$oModel->getObjectList($offset, $limit){rdelim}
{ldelim}assign var=totalObjects value=$oModel->getTotalObjects(){rdelim}
{ldelim}if $objects->getArrayCount() > 0{rdelim}
	<table class="data">
		<thead>
			<tr>
{foreach $daoObjectVars as $value}
				<th{if $value@first} class="first"{/if}>{ldelim}t{rdelim}{$value|replace:"_":""|spacifyCapitalisedString}{ldelim}/t{rdelim}</th>
{/foreach}
				<th class="last">&nbsp;</th>
			</tr>
		</thead>
		<tfoot>
			{ldelim}include file=$oView->getTemplateFile('daoPaging', '/shared') colspan={$daoObjectVars->getArrayCount()-1}{rdelim}
		</tfoot>
		<tbody>
		{ldelim}include file=$oView->getTemplateFile('daoPaging', '/shared') colspan={$daoObjectVars->getArrayCount()-1}{rdelim}
		{ldelim}foreach $objects as $oObject{rdelim}
			<tr class="{ldelim}cycle values="alt,"}">
{foreach $daoObjectVars as $value}
				<td>{ldelim}$oObject->get{$value|replace:"_":""}(){rdelim}</td>
{/foreach}
				<td class="actions">
					{ldelim}include file=$oView->getTemplateFile('daoObjectListDefaultActions', '/shared'){rdelim}
				</td>
			</tr>
		{ldelim}/foreach{rdelim}
		</tbody>
	</table>
{ldelim}else{rdelim}
	<p>{ldelim}t{rdelim}No objects found in system.{ldelim}/t{rdelim}</p>
{ldelim}/if{rdelim}
