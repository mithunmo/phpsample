<h3><a href="#">{t}Broadcast Rights{/t}</a></h3>
<div>
    <div class="formFieldContainer">
        <h4>{t}Date Approved *{/t}</h4>
        <p>
            <input type="text" name="Data[{mofilmDataname::DATA_BROADCAST_DATE}]" class="datepicker broadCastApproveddate" value="{$oMovie->getDataSet()->getProperty(mofilmDataname::DATA_BROADCAST_DATE)}" title="{t}Schedule date in year-month-day format e.g. Jan 1st 2010 is 2010-01-01{/t}" />
        </p>
    </div>
    <div class="formFieldContainer">
        <div class="broadCastCountries">
            {if $oMovie->getBroadcastSet()->getCount()}
                {foreach $oMovie->getBroadcastSet() as $broadCast}
                    {$bIndex = $broadCast@iteration}

                        <div class="broadCastCountryInfo line-bottom" id="CountryBroadcast{$bIndex}">
                            <div class="formFieldContainer floatLeft">
                                <h4>{t}Country*{/t}</h4>
                                <p>{territorySelect id="myselect{$bIndex}" onclick="getCountryID(this.value, {$bIndex})"  name="Broadcast[{$bIndex}][CountryID]" selected=$broadCast->getCountryID()  class="valignMiddle broadCastCountryName" }</p>
                            </div>
                            <div class="formFieldContainer floatLeft">
                               <h4>&nbsp;{t}Broadcast Date *{/t}</h4>
                                &nbsp;<input type="text" name="Broadcast[{$bIndex}][date]" onchange="checkDates(this.value)" class="datepicker broadCastdate" value="{$broadCast->getBroadCastDate()->getDate()}" title="{t} Schedule date in year-month-day format e.g. Jan 1st 2010 is 2010-01-01{/t}" />
                            </div>

                                <div class="removeCurBroadcast formIcon ui-state-default floatLeft" title="Remove this broadcast"  id="{$bIndex}"><span class="ui-icon ui-icon-minusthick"></span></div>

                            <div class="clearBoth"></div>
                        <hr>
                        </div>

            {/foreach}
        {else}
           
           
                <div class="broadCastCountryInfo line-bottom" id="CountryBroadcast{$bIndex+1}">
                    <div class="formFieldContainer floatLeft">
                        <h4>{t}Country *{/t}</h4>
                        <p>{territorySelect id="myselect{$bIndex+1}" onclick="getCountryID(this.value, {$bIndex+1})"  class="broadCastCountryName" name="Broadcast[{$bIndex+1}][CountryID]"}</p>
                    </div>
                    <div class="formFieldContainer floatLeft">
                        <h4>&nbsp;{t}Broadcast Date *{/t}</h4>
                        &nbsp;<input type="text" name="Broadcast[{$bIndex+1}][date]" onchange="checkDates(this.value)" class="datepicker broadCastdate" value="" title="{t}Schedule date in year-month-day format e.g. Jan 1st 2010 is 2010-01-01{/t}" />
                    </div>
                    
                        <div class="removeCurBroadcast formIcon ui-state-default floatLeft" title="Remove this broadcast"  id="{$bIndex+1}"><span class="ui-icon ui-icon-minusthick"></span></div>
                        
                    
                    <div class="clearBoth"></div>
                    <hr>
                </div>
           {/if}
        </div>
    </div> 
                 
   <div class="formFieldContainer">
        <div class="addBroadCastCountry" title="{t}Add Another Country{/t}">+ add another country</div>
        
    </div>
        <input type="hidden" name="broadcastChanged" id="broadcastDataChanged">
    <div class="formFieldContainer">
        <h4>{t}Notes on broadcast licensing requirements:{/t}</h4>
        <p>
            <p><textarea name="Data[{mofilmDataname::DATA_BROADCAST_NOTE}]" rows="6" cols="60" class="long broadCastNote"></textarea></p>
        </p>
    </div>
    
        <h4>{t}Activity Log{/t}</h4>
        {foreach mofilmUserLog::listOfObjects(null, null, null, "{mofilmUserLog::TYPE_OTHER}", "Movie:{$oModel->getMovieID()}") as $activityLog}
            <div class="formFieldContainer">
                <div class="formFieldContainer floatLeft">{date("d/m/Y", strtotime($activityLog->getTimestamp()))}</div><div class="formFieldContainer floatLeft">&nbsp;&nbsp;&nbsp;{strstr($activityLog->getDescription(), " ")} </div>
                <div class="clearBoth"></div>
            </div>
        {/foreach}
        
    
</div>
