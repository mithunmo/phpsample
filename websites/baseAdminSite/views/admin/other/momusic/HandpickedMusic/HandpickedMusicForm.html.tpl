{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
	<table class="data">
		<tbody>
			<tr>
				<th>{t}Cover Image ID{/t}</th>
				<td>
                                    <!-- <input type="text" name="CoverImageID" value="{$oObject->getCoverImageID()}" />-->
                                    <select name="CoverImageID">
                                    {foreach $imageList as $oImage}
                                        <option value="{$oImage->getID()}" {if $oObject->getCoverImageID()  == $oImage->getID()} selected="selected"{/if}>{$oImage->getName()}</option>
                                    {/foreach}	
                                    </select>
                                </td>
			</tr>
			<tr>
				<th>{t}Track ID{/t}</th>
				<td><input type="text" name="TrackID" value="{$oObject->getTrackID()}" /></td>
			</tr>
			<tr>
				<th>{t}Status{/t}</th>
				<td><input type="text" name="Status" value="{$oObject->getStatus()}" /></td>
			</tr>
			<tr>
				<th>{t}Rank{/t}</th>
				<td><input type="text" name="Rank" value="{$oObject->getRank()}" /></td>
			</tr>
                        
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record ID &quot;{$oController->getPrimaryKey()}&quot;?{/t}</p>
{/if}