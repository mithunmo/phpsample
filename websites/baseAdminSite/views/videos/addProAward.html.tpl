<div class="addChangeAward">
    <img src="/themes/shared/icons/medal_gold_add.png" alt="{t}Add an Award{/t}" title="{t}Add an Award{/t}" class="smallIcon" />
    <span>{t}Add / Change Award{/t}</span>
</div>

<div class="addChangeAwardForm" title="{t}Add / Change Award{/t}" style="display: none;">
	<fieldset class="addChangeFormOptions">	
            
                <input type="checkbox" name="BestOfClientAward" id="bocCheckbox" value="BestOfClients" {if !$awardBestOfClient}checked="checked"{/if} />
                <label for="bocCheckbox">{t}Best Of Client{/t}</label>
                {if $oController->hasAuthority('canManageProVideoAwards') }
                    <input type="radio" name="Award" id="awardshowcase" value="ProShowcase" {if $movieAwards == 'ProShowcase' }checked="checked"{/if}  />
                    <label for="awardshowcase">{t}Pro Showcase{/t}</label>

                    <input type="radio" name="Award" id="awardfinal" value="ProFinal" {if $movieAwards == 'ProFinal' }checked="checked"{/if}  />
                    <label for="awardfinal">{t}Pro Final{/t}</label>
                {/if} 
		             
		{if $oController->hasAuthority('canRemoveAllVideoAwards')}
			<input type="radio" name="Award" id="award5" value="remove" />
			<label for="award5">{t}Remove All Awards{/t}</label>
		{/if}
	</fieldset>
	<span class="awardFormSave">{t}Save Changes{/t}</span>
</div>