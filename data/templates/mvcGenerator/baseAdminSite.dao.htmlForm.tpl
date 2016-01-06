{ldelim}if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'{rdelim}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{ldelim}$oController->getPrimaryKey()|default:0{rdelim}" /></div>
	<table class="data">
		<tbody>
{foreach item=value from=$daoObjectVars}
			<tr>
				<th>{ldelim}t{rdelim}{$value|replace:"_":""|spacifyCapitalisedString}{ldelim}/t{rdelim}</th>
				<td><input type="text" name="{$value|replace:"_":""}" value="{ldelim}$oObject->get{$value|replace:"_":""}(){rdelim}" /></td>
			</tr>
{/foreach}
		</tbody>
	</table>
{ldelim}elseif $oController->getAction() == 'deleteObject'{rdelim}
	<p>{ldelim}t{rdelim}Are you sure you want to delete record {if $daoRecordDisplayMethod}named &quot;{ldelim}$oObject->{$daoRecordDisplayMethod}(){rdelim}&quot;{else}ID &quot;{ldelim}$oController->getPrimaryKey(){rdelim}&quot;{/if}?{ldelim}/t{rdelim}</p>
{ldelim}/if{rdelim}