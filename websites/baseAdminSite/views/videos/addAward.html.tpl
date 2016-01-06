    <div class="addChangeAward">
	{if $oMovie->getAwardSet($oMovie->getSource()->getEventID())->isWinner()}
		<img src="/themes/shared/icons/medal_gold_1.png" alt="{t}Event Winner{/t}" title="{t}Event Winner{/t}" class="smallIcon" />
	{elseif $oMovie->getAwardSet($oMovie->getSource()->getEventID())->isRunnerUp()}
		<img src="/themes/shared/icons/medal_silver_1.png" alt="{t}Event Runner Up{/t}" title="{t}Event Runner Up{/t}" class="smallIcon" />
	{elseif $oMovie->getAwardSet($oMovie->getSource()->getEventID())->isFinalist()}
		<img src="/themes/shared/icons/medal_bronze_1.png" alt="{t}Finalist for Event{/t}" title="{t}Finalist for Event{/t}" class="smallIcon" />
	{elseif $oMovie->getAwardSet($oMovie->getSource()->getEventID())->isShortlisted()}
		<img src="/themes/shared/icons/medal_bronze_1.png" alt="{t}Shortlisted for Event{/t}" title="{t}Shortlisted for Event{/t}" class="smallIcon" />
	{else}
		<img src="/themes/shared/icons/medal_gold_add.png" alt="{t}Add an Award{/t}" title="{t}Add an Award{/t}" class="smallIcon" />
	{/if}
	
	<span>{t}Add / Change Award{/t}</span>
</div>

<div class="addChangeAwardForm" title="{t}Add / Change Award{/t}" style="display: none;">
	<fieldset class="addChangeFormOptions">	
                <input type="checkbox" name="BestOfClientAward" id="bocCheckbox" value="BestOfClients" {if !$awardBestOfClient}checked="checked"{/if} />
                <label for="bocCheckbox">{t}Best Of Client{/t}</label>  
                {if $oController->hasAuthority('canManageVideoAwards') }
                    <fieldset id="awardPositionHolder" class="hidden">
                            <label for="awardPosition">{t}Position{/t}</label>
                            <select name="Position" id="awardPosition" size="1">
                                    {for $x = 1 to 10}
                                    <option value="{$x}">{$x}</option>
                                    {/for}
                            </select>
                    </fieldset>
                    <input type="radio" name="Award" id="award1" value="Winner" {if $movieAwards == 'Winner' }checked="checked"{/if} />
                    <label for="award1">{t}Event Winner{/t}</label>

                    <input type="radio" name="Award" id="award3" value="Finalist" {if $movieAwards == 'Finalist' }checked="checked"{/if} />
                    <label for="award3">{t}Winner{/t}</label>

                    <input type="radio" name="Award" id="award4" value="Shortlisted" {if $movieAwards == 'Shortlisted' }checked="checked"{/if} />
                    <label for="award4">{t}Short Listed{/t}</label>

                    <input type="radio" name="Award" id="award2" value="Runner Up" {if $movieAwards == 'Runner Up' }checked="checked"{/if} />
                    <label for="award2">{t}Runner Up{/t}</label>
                {/if}
               
                
		{if $oController->hasAuthority('canRemoveAllVideoAwards')}
			<input type="radio" name="Award" id="award5" value="remove" />
			<label for="award5">{t}Remove All Awards{/t}</label>
		{/if}
	</fieldset>
	<span class="awardFormSave">{t}Save Changes{/t}</span>
</div>