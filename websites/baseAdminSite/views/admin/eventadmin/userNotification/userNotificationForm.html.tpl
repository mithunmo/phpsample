{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
    <div class="hidden"><input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" /></div>
    <table class="data">
        <tbody>


            <tr>
                <td>{t}Choose the Event{/t}</td>
                <td>
                    <select id ="eventList" name="EventID">
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
                    <select id="eventListSources" name="SourceID">
                    </select>	
                </td>
                <td align="left"><span id="msg_source" style="color:red"></span></td>
            </tr>
<!--
            <tr>
                <th>{t}Source ID{/t}</th>
                <td><input type="text" name="SourceID" value="{*$oObject->getSourceID()*}" /></td>
            </tr>
-->
            <tr>
                <th>{t}Title{/t}</th>
                <td><input type="text" name="Title" value="{$oObject->getTitle()}" /></td>
            </tr>
        <input type="text" name="Status" hidden="hidden" value="0" />
    </tbody>
</table>
{elseif $oController->getAction() == 'deleteObject'}
    <p>{t}Are you sure you want to delete record named &quot;{$oObject->getTitle()}&quot;?{/t}</p>
{/if}