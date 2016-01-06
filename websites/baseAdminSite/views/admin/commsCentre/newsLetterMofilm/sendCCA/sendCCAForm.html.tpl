{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
	<div class="hidden">
		<input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" />
		<input type="hidden" name="MessageType" value="1">
		<input type="hidden" name="NewsletterType" value="2">
	</div>
	<div>
		<input type="radio" name="Type" value="shortlist" id="selectCategory1" /> Shortlist / Non-Shortlist
		<input type="radio" name="Type" value="nonwinners" id="selectCategory2" /> Non Winners
	</div>
	<br />
	<table class="data">
		<tbody>
			<tr style="display: none" class="shortlistedDisplay">
				<th>{t}Shortlisted email{/t}</th>
				<td>
					<select name="NlidS">
						<option value="">Not selected</option>
						{foreach $shortlisted as $oNewsletter}
							<option value="{$oNewsletter->getNlid()}" {if $oNewsletter->getNlid() == $oObject->getNewsletterID()} selected="selected"{/if}> {$oNewsletter->getName()} </option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr style="display: none" class="shortlistedDisplay">
				<th>{t}Not Shortlisted email{/t}</th>
				<td>
					<select name="NlidNs">
						<option value="">Not selected</option>
						{foreach $nonshortlisted as $oNewsletter}
							<option value="{$oNewsletter->getNlid()}" {if $oNewsletter->getNlid() == $oObject->getNewsletterID()} selected="selected"{/if}> {$oNewsletter->getName()} </option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr style="display: none" class="winnerDisplay">
				<th>{t}Non Winners{/t}</th>
				<td>
					<select name="NlidNw">
						<option value="">Not selected</option>
						{foreach $nonwinners as $oNewsletter}
							<option value="{$oNewsletter->getNlid()}" {if $oNewsletter->getNlid() == $oObject->getNewsletterID()} selected="selected"{/if}> {$oNewsletter->getName()} </option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<th>{t}Event{/t}</th>
				<td>{eventSelect id="eventListCCA" name='EventID' selected=$searchEventID class="valignMiddle string" user=$oUser}</td>
			</tr>
			<tr>
				<th>{t}Brand{/t}</th>
				<td>
					{if $searchEventID}
						{sourceSelect id="eventListSourcesCCA" name='SourceID[]' selected=$searchSourceID multiple=mulitple eventID=$searchEventID class="valignMiddle string" user=$oUser}
					{else}
						<select id="eventListSourcesCCA" name="SourceID[]" multiple=multiple size="1" class="valignMiddle string"><option>{t}Select event{/t}</option></select>
					{/if}					
				</td>
			</tr>
			<tr style="display: none" class="shortlistedDisplay">
				<th>{t}Movie rating{/t}</th>
				<td>
					<select name="videoRating">
						<option value="">Not selected</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8" selected="selected">8</option>
						<option value="9">9</option>
						<option value="10">10</option>
					</select>
				</td>
			</tr>
			
			<!--tr>
				<th>{t}Attachement form (PDF){/t}</th>
				<td><input type="file" name="Cca" id="CCAForm" class="string" /><br /></td>
			</tr-->
			<tr>
				<th>{t}Email Name{/t}</th>
				<td>
					<select name="EmailName">
						{foreach $emailName as $oEmailName}
							<option value="{$oEmailName->getID()}" {if $oEmailName->getID() == $oObject->getEmailName()} selected="selected"{/if}> {$oEmailName->getSenderEmail()} </option>
						{/foreach}
					</select>	
				</td>
			</tr>
			<tr>
				<th>{t}Scheduled Date{/t}</th>
				<td><input type="text" name="ScheduledDate" id="templateTimePicker" value="{$oObject->getScheduledDate()}"></td>
			</tr>
		</tbody>
	</table>
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record ID &quot;{$oController->getPrimaryKey()}&quot;?{/t}</p>
{/if}