{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
<div class="hidden">
	<input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}"/>
	<input type="hidden" name="Status" value="0">
	<input type="hidden" name="NewsletterType" value="1">
</div>
<div id="fieldWrapper">
	<span id="sendnewsletterwizard">
	</span>
	<span class="step" id="first">
		<div class="firstdiv">
			<label class="labelsize" for="newsletter">{t}Select the newsletter{/t}</label>
			<select name="Nlid">
				{foreach $newsletter as $fileall}
					<option value="{$fileall->getNlid()}" {if $fileall->getNlid() == $oObject->getNewsletterID()} selected="selected"{/if}> {$fileall->getName()} </option>
				{/foreach}
			</select>
		</div>
		<div class="firstdiv">
			<label class="labelsize" for="date">{t}Scheduled Date{/t}</label>
			<input type="text" name="ScheduledDate" id="templateTimePicker" value="{$oObject->getScheduledDate()}">
		</div>
		<div class="firstdiv">
			<label class="labelsize" for="filterList">{t}Choose the type of List{/t}</label>
			<select class="input_field_12em link required">
				<option value="event">Event</option>
				<option value="subscription">Subscription</option>
			</select>
		</div>
		<div class="firstdiv">
			<label class="labelsize" for="senderemail">{t}Choose the sender email address{/t}</label>
			<select name="EmailName">
			{foreach $emailName as $oEmailName}
				<option value="{$oEmailName->getID()}" {if $oEmailName->getID() == $oObject->getEmailName()} selected="selected"{/if}> {$oEmailName->getSenderEmail()} </option>
			{/foreach}
			</select>
		</div>
		<div class="firstdiv">
			<label class="labelsize" for="messagetype">{t}Choose the message type{/t}</label>
			<select name="MessageType">
				<option value="0">Marketing Message </option>
				<option value="1">Important Message </option>
			</select>
		</div>
	</span>

	<span id="subscription" class="step submit_step">
		<div class="firstdiv">
			<label class="labelsize" for="subs">{t}Choose the subscription list{/t}</label>
			<select name="Params_list" id="sendNewsletterSublist">
				<option value=""> Choose the List </option>
				{foreach $lists as $oList}
					<option value="{$oList->getID()}" {if $oList->getID() == $oObject->getParams()} selected="selected"{/if}> {$oList->getName()}</option>
				{/foreach}
			</select>
		</div>
	</span>

	<span id="event" class="step submit_step">
		<div class="firstdiv">
			<label class="labelsize" for="username">{t}Select the filter{/t}</label>
			<select name="EventParams" id="sendNewsletterEventParamslist">
				{if $oController->getAction() == 'editObject'}
					{foreach $filterObj as $oFilter}
						<option value="{$oFilter->getID()}" {if $oModel->getParams() == $oFilter->getID()} selected="selected"{/if}> {$oFilter->getDescription()} </option>
					{/foreach}
				{else}
					<option value=""> Choose the filter </option>
					<option value="1"> Event List based on Brief Downloads </option>
					<option value="2"> Event List based on Video uploads </option>
					<option value="3"> Event List based on Rating above 5 </option>
				{/if}	
			</select>
		</div>
		<div class="firstdiv">
			<label  class="labelsize" for="cevent">Choose the event</label>
			<select name="EventID" id="sendNewsletterEventlist">
				<option value=""> Choose the event </option>
				{foreach $eventsall as $oEvent}
					<option value="{$oEvent->getID()}" {if $oEvent->getID() == $oModel->getEventID($oObject->getParams())} selected="selected"{/if}> {$oEvent->getName()} </option>
				{/foreach}
			</select>
		</div>
		<div class="firstdiv">
			<label class="labelsize" for="Rating">Rating</label>
			<select name="videoRating" id="videoRatingID" disabled="disabled">
				<option value="">Not selected</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10</option>
			</select>
		</div>
	</span>
</div>
<div id="Navigation">
	<input class="navigation_button" id="back" value="Back" type="reset" />
	<input class="navigation_button" id="next" value="Next" type="submit" />
</div>

{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record ID &quot;{$oController->getPrimaryKey()}&quot;?{/t}</p>
{/if}