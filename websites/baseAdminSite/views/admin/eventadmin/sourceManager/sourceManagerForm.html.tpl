{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
{assign var=sourcePrimaryID value=$oController->getPrimaryKey()|default:0}  
{assign var=searchCorporateID value=$oController->getCorporate($oObject->getBrandID())}
	<div class="hidden">
	    <input type="hidden" name="PrimaryKey" value="{$sourcePrimaryID}" />
            <input type="hidden" name="Name" id='brandName' value="{$oObject->getName()}" class="string" /></td>
	     <input type="hidden" name="BrandID" id='brandID' value="{$oObject->getBrandID()}" class="string" /></td>   
        <input type="hidden" name="SourceDataSetID" value="{$oObject->getSourceDataSet()->getID()|default:0}" />
	    <input type="hidden" name="GrantID" value="{$oObject->getGrants()->getID()|default:0}" />
	    <input type="hidden" name="Hash" value="{$oObject->getSourceDataSet()->getHash()|default:0}" />
	</div>
	<div class="content">	
		<div class="daoAction">
			{if $oObject->getSourceDataSet()->getHash()}
				<a href="http://www.mofilm.com/competitions/previewBrand/{$oObject->getSourceDataSet()->getHash()}" title="{t}Preview{/t}" target="_blank">
					<img src="/themes/shared/icons/email.png" alt="{t}Preview{/t}" class="icon" />
						{t}Preview{/t}
				</a>
			{/if}
				{*<a href="#?height=450&width=900&modal=false" title="{t}Publish{/t}" id="" class="thickbox">
					<img src="/themes/shared/icons/email.png" alt="{t}Publish{/t}" class="icon" />
					{t}Publish{/t}
				</a>*}
		</div>
		<div class="clearBoth"></div>
	</div>
	<div id="userFormAccordion">
                <h3><a href="#">Add particpating Brand to a project</a></h3>
		<div>
			<table class="data">
				<tbody>
					<tr>
						<th>{t}Project name{/t}</th>
						<td>{eventSelect id="projectEventID" name='EventID' selected=$oObject->getEventID()}</td>
					</tr>

                                </tbody>
                        </table>
                </div>                    
{*                                        
		<h3><a class="brandDetails" href="#">Brand Details</a></h3>
		<div>
			<table class="data">
				<tbody>
                                        <tr>
						<th>{t}Corporate Name{/t}</th>
						<td>{corporateDistinctSelect id="eventListCorporates" name='CorporateID' selected=$searchCorporateID class="valignMiddle string" }</td>
					</tr>
                                        <tr>
						<th>{t}Brand Name{/t}</th>
                                                <td>
                                                {if $searchCorporateID}
                                                        {brandSelect id="corporateListBrands" name='BrandIDSelect' selected=$oObject->getBrandID() corporateID=$searchCorporateID class="valignMiddle string" }
                                                {else}
                                                        {brandDistinctSelect id="corporateListBrands" name='BrandIDSelect' selected=$oObject->getBrandID() class="valignMiddle string" }       
                                                {/if}
                                                </td>
					</tr>
                                        <tr>
                                            
                                            	<th>{t}Sponsor{/t}</th>
                                            <td>
                                            <select name="SponsorID">                                                
                                                <option value=""> Select the user</option>                                             
                                            {foreach $userList as $oAdminUser}                                                
                                                <option {if $oObject->getSponsorID() == $oAdminUser->getID()}selected="{$oAdminUser->getID()}" {/if} value="{$oAdminUser->getID()}">{$oAdminUser->getFullname()} </option>                                             
                                            {/foreach}
                                            </select>    
                                            </td>
                                        </tr>    
					<tr>
						<th>{t}Invite Only{/t}</th>
						<td>{yesNoSelect name='Hidden' selected=$oObject->getHidden()}</td>
					</tr>
					<tr>
						<th>{t}Custom Design{/t}</th>
						<td>{yesNoSelect name='Custom' selected=$oObject->getCustom()}</td>
					</tr>
					<tr>
						<th>{t}Start Date{/t}</th>
						<td>
							{t}Use Event Submission Start Date{/t}
							<input class="sourceManagerDate" type="checkbox" name="UseEventStartDate" title="{t}Tick to use the event Start Date{/t}" value="1" {if !$oObject->getStartDate()}checked="checked"{/if} />
							<br />
							
							<input type="text" name="Startdate" value="{$oObject->getStartDate()|date_format:'%Y-%m-%d'}" class="date datepicker" />
							<strong>@</strong>
							{html_select_time field_array='StartdateTime' prefix='' display_seconds=false minute_interval=10 time=$oObject->getStartDate()}
						</td>
					</tr>
					<tr>
						<th>{t}End Date{/t}</th>
						<td>
							{t}Use Event Submission End Date{/t}
							<input class="sourceManagerDate" type="checkbox" name="UseEventEndDate" title="{t}Tick to use the event End Date{/t}" value="1" {if !$oObject->getEndDate()}checked="checked"{/if} />
							<br />

							<input type="text" name="Enddate" value="{$oObject->getEndDate()|date_format:'%Y-%m-%d'}" class="date datepicker" />
							<strong>@</strong>
                                                        {if $oObject->getEndDate() != ''}
                                                            {assign var=sourceEndDate value=$oObject->getEndDate()}
                                                        {else}
                                                            {assign var=sourceEndDate value=date('Y-m-d 23:50:00')}
                                                        {/if}
							{html_select_time field_array='EnddateTime' prefix='' display_seconds=false minute_interval=10 time=$sourceEndDate}
						</td>
					</tr>
					<tr>
						<th>{t}Title{/t}</th>
						<td><input type="text" name="DisplayName" value="{$oObject->getSourceDataSet()->getName()|escape:'htmlall':'UTF-8'}" class="string" /></td>
					</tr>
					<tr>
						<td colspan="5"><br />
							<b>{t}Sitecopy{/t}</b><br /><br />
							<textarea name="Sitecopy" rows="20" cols="40" class="tinymce">{$oObject->getSourceDataSet()->getDescription()|escape:'htmlall':'UTF-8'}</textarea>
						<br /></td>
					</tr>
					<tr>
						<td colspan="5"><br />
							<b>{t}Terms{/t}</b><br /><br />
							<textarea name="Terms" rows="20" cols="40" class="tinymce">{$oObject->getSourceDataSet()->getTerms()|escape:'htmlall':'UTF-8'}</textarea>
						<br /></td>
					</tr>
					<tr>
						<th>{t}Instructions{/t}</th>
						<td>
							<textarea name="Instructions" rows="5" cols="60">{$oObject->getInstructions()|escape:'htmlall':'UTF-8'}</textarea>
						</td>
					</tr>
					<tr>
						<th>{t}Status{/t}</th>
						<td colspan="4">{sourceStatusSelect name='Status' selected=$oObject->getSourceStatus()}</td>
					</tr>
				</tbody>
			</table>
		</div>
		<h3><a href="#">Brand Prize</a></h3>
		<div>
			<div>
				{t}Winner Trip Budget in $ {/t} <input type="text" name="Tripbudget" value="{$oObject->getTripbudget()|default:6000}" class="small" />
			</div>
			<table id="prizeData" class="data">
				<thead>
					<tr>
						<th>Position</th>
						<th>Amount in $</th>
						<th>Display Description</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					{foreach $oObject->getSourcePrizeSet() as $oPrize}
					<tr>
						<td>{$oPrize->getPosition()}</td>
						<td>{$oPrize->getAmount()}</td>
						<td>{$oPrize->getDescription()}</td>
						<td>
							<div class="removeCurPrize formIcon ui-state-default floatLeft" title="Remove Prize" id="{$oPrize->getID()}">
								<span class="ui-icon ui-icon-minusthick">
								</span>
							</div>
<!--							<input type="hidden" name="Prize[{$index1 = $oPrize@iteration}{$index1}][ID]" value="{$oPrize->getID()}" />
							<input type="checkbox" name="Prize[{$index1}][Remove]" value="1" class="addRemovePrizeControl" />-->
						</td>
					</tr>
					{/foreach}
					<tr>
						<td><input type="hidden" name="Prize[{$index1+1}][ID]" value="0" />
						    <input type="text" class="prizePosition small" name="Prize[{$index1+1}][Position]" value="" />
						</td>
						<td><input type="text" class="prizeAmount small" name="Prize[{$index1+1}][Amount]" value="" /></td>
						<td><textarea class="prizeDescription" name="Prize[{$index1+1}][Description]" rows="1" cols="40"></textarea></td>
						<td><input type="text" class="prizeDescription medium" name="Prize[{$index1+1}][Description]" value="" /></td>
						<td>
							<div class="removeCurPrize formIcon ui-state-default floatLeft" title="Remove Prize" id="new">
								<span class="ui-icon ui-icon-minusthick">
								</span>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="prizeControls hidden">
				<div class="floatRight">
					<div class="addPrize formIcon ui-state-default floatLeft" title="{t}Add New Prize{/t}"><span class="ui-icon ui-icon-plusthick"></span></div>
					<div class="removePrize formIcon ui-state-default floatLeft" title="{t}Remove Last Prize{/t}"><span class="ui-icon ui-icon-minusthick"></span></div>
				</div>
				<div class="clearBoth"></div>
			</div>
		</div>
		{if $oController->getAction() == 'editObject'}
		<h3><a href="#">Design Images</a></h3>
		<div>
			<table class="data">
				<tbody>
					<tr>
						<th class="valignTop">
							{t}Upload Logo{/t}<br />
							<img src="{$adminEventFolder}/{$oObject->getLogoName()}.jpg" width="50" height="28" alt="{$oObject->getName()}" title="{$oObject->getName()}" class="valignMiddle" style="border: 1px solid #000;" />
						</th>
						<td colspan="4">
							<input type="file" id="SourceLogo" name="SourceLogo" class="string" /><br />
							{t}Please note:{/t}<br />
							<em>
								{t}Logos will be uploaded using the source name without any punctuation or special characters.{/t}<br />
								{t}Logos will be resized to 261x139 (client) and 50x28 (admin) in JPEG format.{/t} 
							</em>
						</td>
					</tr>
					<tr>
						<th class="valignTop">
							{t}Upload Banner{/t}<br />
						</th>
						<td colspan="4">
							<img src="{$adminEventFolder}/{$oObject->getLogoName()}.jpg" width="50" height="28" alt="{$oObject->getName()}" title="{$oObject->getName()}" class="valignMiddle" style="border: 1px solid #000;" />
							<br />
							<input type="file" name="SourceBanner" id="SourceBanner" class="string" />
							<br />
						</td>
					</tr>
					<tr>
						<th class="valignTop">
							{t}Upload Filler{/t}<br />
						</th>
						<td colspan="4">
							<img src="{$adminEventFolder}/{$oObject->getLogoName()}.jpg" width="50" height="28" alt="{$oObject->getName()}" title="{$oObject->getName()}" class="valignMiddle" style="border: 1px solid #000;" />
							<br />
							<input type="file" name="SourceFiller" id="SourceFiller" class="string" />
							<br />
						</td>
					</tr>
					<tr>
						<th class="valignTop">
							{t}Background Color{/t}<br />
						</th>
						<td colspan="4">
							<br />
							<input type="text" name="SourceBgcolor" value="{$oObject->getBgcolor()}" class="small string" />
							<br />
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		{/if}
		<h3><a href="#">Brand Brief & Assets</a></h3>
		<div>
			<table id="DownloadFileData" class="data">
				<thead>
					<tr>
						<th>Filetype</th>
						<th>Description</th>
						<th>Download Uri</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					{foreach $oObject->getSourceDownloadFiles() as $oDlFiles}
					<tr>
						<td>{$oDlFiles->getFiletype()}</td>
						<td>{$oDlFiles->getDescription()}</td>
						<td>{foreach $oDlFiles->getSourceSet() as $oSource}
							{if !($oDlFiles->isExtenalLink())}
							    <a target="_blank" href="
										{if $oSource->isOpen()}
											https://mofilm.com/brief/{$oSource->getDownloadHash()}
										{else}
											http://admin.mofilm.com/download/generalDownloads?url={$oDlFiles->getFileLocation()}
										{/if}">
										Link
							    </a>
							{else}
							    <a target="_blank" href="{$oDlFiles->getFilename()}">Link</a>
							{/if}
						    {/foreach}
						</td>
						<td>
							{if !($oDlFiles->isExtenalLink())}
								<input type="file" id="FileUpload{$oDlFiles->getID()}" name="Files" class="small" />
							{/if}
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
			<div class="DownloadFileDataControls hidden" style="padding-top: 10px; padding-bottom: 10px;">
				<div class="floatRight">
					<div class="addAsset formIcon ui-state-default floatLeft" title="{t}Add Asset{/t}">Add Asset</div>
				</div>
				<div class="floatRight" style="padding-right: 10px;">
					<div class="addBrief formIcon ui-state-default floatLeft" title="{t}Add Brief{/t}">Add Brief</div>
				</div>
				<div class="floatRight" style="padding-right: 10px;">
					<div class="addNda formIcon ui-state-default floatLeft" title="{t}Add NDA{/t}">Add NDA</div>
				</div>
				<div class="clearBoth"></div>
			</div>
		</div>
		<h3><a href="#">Tracks</a></h3>
		<div>
			<table id="trackData" class="data">
				<thead>
					<tr>
						<th>#</th>
						<th>Artist</th>
						<th>Title</th>
						<th>Supplier</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					{foreach $oObject->getTrackSet() as $oTrack}
					<tr>
						<td>
							<input type="hidden" name="Tracks[{$index = $oTrack@iteration}{$index}][ID]" value="{$oTrack->getID()}" />
							<input type="hidden" name="Tracks[{$index = $oTrack@iteration}{$index}][Hash]" value="{$oTrack->getDownloadHash()}" />
							<span class="recordNumber">{$index}</span>
						</td>
						<td><a href="/admin/eventadmin/trackManager/editObject/{$oTrack->getID()}" title="Edit track details">{$oTrack->getArtist()}</a></td>
						<td><a href="/admin/eventadmin/trackManager/editObject/{$oTrack->getID()}" title="Edit track details">{$oTrack->getTitle()}</a></td>
						<td>{$oTrack->getSupplier()->getDescription()}</td>
						<td><input type="checkbox" name="Tracks[{$index}][Remove]" value="1" class="addRemoveControl" /></td>
					</tr>
					{/foreach}
					<tr>
						<td><input type="hidden" name="Tracks[{$index+1}][ID]" value="0" /><span class="recordNumber">{$index+1}</span></td>
						<td><input type="text" class="trackArtist" name="Tracks[{$index+1}][Artist]" value="" /></td>
						<td><input type="text" class="trackTitle" name="Tracks[{$index+1}][Title]" value="" /></td>
						<td><div class="ui-widget"><input type="text" class="trackSupplier" name="Tracks[{$index+1}][Supplier]" value="" /></div></td>
						<td><input type="checkbox" name="Tracks[{$index+1}][Remove]" value="1" class="addRemoveControl" title="{t}Tick box to remove track{/t}" /></td>
					</tr>
				</tbody>
			</table>
			<div class="controls hidden">
				<div class="floatRight">
					<div class="addTrack formIcon ui-state-default floatLeft" title="{t}Add New Track{/t}"><span class="ui-icon ui-icon-plusthick"></span></div>
					<div class="removeTrack formIcon ui-state-default floatLeft" title="{t}Remove Last Track{/t}"><span class="ui-icon ui-icon-minusthick"></span></div>
				</div>
				<div class="clearBoth"></div>
			</div>
		</div>

		<h3><a href="#">Grants</a></h3>
		<div>
			<table class="data">
				<tbody> 
					<tr>
						<th>{t}Grants Available{/t}</th>
						
						<td>
							<select id="grantsAvailable" name="GrantsAvailability">
							    <option value="Y" {if $oObject->getGrants()->getID() > 0 }selected{/if}>Yes</option>
							    <option value="N" {if !($oObject->getGrants()->getID() > 0) }selected{/if}>No</option>
							</select>
						</td>
					</tr>
					
					<tr class="grantsTab" style="display:{if $oObject->getGrants()->getID() == 0 }none{/if}">
						<th>{t}Deadline	{/t}</th>
						<td>
							<input type="text" name="GrantsDeadline" value="{$oObject->getGrants()->getEndDate()->getDate()|date_format:'%Y-%m-%d'}" class="date datepicker" />
							<strong>@</strong>
							{html_select_time field_array='GrantsDeadlineTime' prefix='' display_seconds=false minute_interval=10 time=$oObject->getGrants()->getEndDate()->getTime()}
						</td>
					</tr>
					<tr class="grantsTab" style="display:{if $oObject->getGrants()->getID() == 0 }none{/if}">
						<th>{t}Grant Pot Size in ${/t}</th>
						<td>
							<input type="hidden" name="CurrencySymbol" value="$" class="small" />
							<input type="text" name="maxGrants" value="{$oObject->getGrants()->getTotalGrants()}" class="small" />
						</td>
					</tr>					
					<tr class="grantsTab" style="display:{if $oObject->getGrants()->getID() == 0 }none{/if}">
						<th>{t}Grants Description{/t}</th>
						<td>
							<textarea name="GrantsDescription" rows="5" cols="60">{$oObject->getGrants()->getDescription()|escape:'htmlall':'UTF-8'}</textarea>
						</td>
					</tr>
					{if $oObject->getGrants()->getID() > 0}
						<tr class="grantsTab" style="display:{if $oObject->getGrants()->getID() == 0 }none{/if}">
							<th>{t}Grants Link Button{/t}</th>
							<td>
								<input type="text" name="grantsLink" value="https://mofilm.com/accounts/grants/apply/{$oController->getPrimaryKey()}" class="long" onclick="this.focus();this.select();" readonly="readonly" />
							</td>
						</tr>
					{/if}
					
				</tbody>
			</table>
		</div>
*}
	</div>
	
	<script type="text/javascript">

	var availableSuppliers = {$availableSuppliers};
	
  
	</script>
	
{elseif $oController->getAction() == 'deleteObject'}
	<p>{t}Are you sure you want to delete record named &quot;{$oObject->getName()}&quot;?{/t}</p>
{/if}