{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
    <div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
    <table class="data">
        <tbody>
            <tr>
                <td>{t}Choose the Event{/t}</td>
                <td>
                    <select id ="eventUpload" name="EventID">
                        <option value="">{t}Select event{/t}</option>
                        {foreach $eventsall as $oEvent}
                            <option value="{$oEvent->getID()}" {if $eventID  == $oEvent->getID()} selected="selected"{/if}>{$oEvent->getName()}</option>
                        {/foreach}	
                    </select>	
                </td>
                <td align="left"><span id="msg_event" style="color:red"></span></td>
            </tr>
            <tr>
                <td>{t}Choose the Brand{/t}</td>
                <td>					
                    <select id="sourceUpload" name="BrandID">
                    </select>	
                </td>
                <td align="left"><span id="msg_source" style="color:red"></span></td>
            </tr>

<!--
            <tr>
                <th>{t}Brand ID{/t}</th>
                <td><input type="text" name="BrandID" value="{$oObject->getBrandID()}" /></td>
            </tr>
-->
            <tr>
                <th>{t}Track List{/t}</th>
                <td><input type="text" name="TrackList" value="{$oObject->getTrackList()}" /></td>
            </tr>
            <tr>
                <th>{t}Status{/t}</th>
                <td><input type="text" name="Status" value="{$oObject->getStatus()}" /></td>
            </tr>
        </tbody>
    </table>
{elseif $oController->getAction() == 'deleteObject'}
    <p>{t}Are you sure you want to delete record ID &quot;{$oController->getPrimaryKey()}&quot;?{/t}</p>
{/if}