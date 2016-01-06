{ldelim}if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'{rdelim}
<form id="ajaxFormData" name="ajaxFormData" method="post" action="">
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{ldelim}$oController->getPrimaryKey()|default:0{rdelim}" /></div>
	<table class="data">
		<tbody>
{foreach $daoObjectVars as $value}
			<tr>
				<th>{$value|replace:"_":""|spacifyCapitalisedString}</th>
				<td><input type="text" name="{$value|replace:"_":""}" value="{ldelim}$oObject->get{$value|replace:"_":""}(){rdelim}" /></td>
			</tr>
{/foreach}
		</tbody>
	</table>
</form>
{ldelim}elseif $oController->getAction() == 'deleteObject'{rdelim}
<p>Are you sure you want to delete record {if $daoRecordDisplayMethod}named &quot;{ldelim}$oObject->{$daoRecordDisplayMethod}(){rdelim}&quot;{else}ID &quot;{ldelim}$oController->getPrimaryKey(){rdelim}&quot;{/if}?</p>
{ldelim}/if{rdelim}