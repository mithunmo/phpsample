{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Apply for Grants{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}
<div id="body">
	<div class="container">
		{if $oUser->getID() == $oGrant->getUserID()}
			<div class="editTitle">
				<span class="imgWrap"><img class="valignMiddle" width="50" height="28" border="0" title="Event: {$oGrant->getGrants()->getSource()->getEvent()->getName()}" alt="{$oGrant->getGrants()->getSource()->getEvent()->getName()}" src="/resources/client/events/{$oGrant->getGrants()->getSource()->getEvent()->getLogoName()}.jpg"></span>
				<span class="imgWrap"><img class="valignMiddle" width="50" height="28" border="0" title="Source: {$oGrant->getGrants()->getSource()->getName()}" alt="{$oGrant->getGrants()->getSource()->getName()}" src="/resources/client/sources/{$oGrant->getGrants()->getSource()->getLogoName()}.jpg"></span>
				<h2>{t}Grant Details for{/t} - {$oGrant->getFilmTitle()}</h2>
			</div>

			<div class="content">
				<div class="daoAction">
					<a href="javascript:history.go(-1);" title="{t}Previous Page{/t}">
						<img src="{$themeicons}/32x32/action-back.png" alt="{t}Previous Page{/t}" class="icon" />
						{t}Previous Page{/t}
					</a>
				</div>
				<div class="clearBoth"></div>
			</div>
			<div class="content">
				{if !($oGrant->getApplicationAppliedStatus())}
					<div class="spanred">Application applied past deadline.</div>
				{/if}
				<h3>{t}Moderation Details{/t}</h3>
				<div class="formFieldModerator">
					<h3><strong>{t}Status{/t}</strong> : {t}{$oGrant->getStatus()}{/t}</h3>
				</div>
				{if $oGrant->getGrantedAmount() > 0 }
					<div class="formFieldModerator">
						<h3><strong>{t}Granted Amount{/t}</strong> : {t}{$oGrant->getGrants()->getCurrencySymbol()} {$oGrant->getGrantedAmount()}{/t}</h3>
					</div>
				{/if}
				<br class="clearBoth">
			</div>
			<div class="content">
				<div class="formFieldContainer">
					<h4>{t}Film Concept{/t}</h4>
					<div class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom">
						<p class="grantsview">{$oGrant->getFilmConcept()|nl2br}</p>
					</div>
				</div>

				<div class="formFieldContainer">
					<h4>{t}Usage of Grants{/t}</h4>
					<div class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom">
						<p class="grantsview">{$oGrant->getUsageOfGrants()|nl2br}</p>
					</div>
				</div>

				<div class="formFieldContainer">
					<h4>{t}Script{/t}</h4>
					<div class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom">
						<p class="grantsview">{$oGrant->getScript()|nl2br}</p>
					</div>
				</div>
				<div class="formFieldContainer">
				<h4>{t}Requested Amount{/t}</h4>
					<table width="100%" class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom" style="padding-top:20px; padding-left: 40px;">
						<tr>
							<td width="33">{t}Script writer{/t}</td>
							<td width="93"><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('ScriptWriter')}</td>
							<td width="33">{t}Producer{/t}</td>
							<td width="93"><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('Producer')}</td>
							<td width="33">{t}Director{/t}</td>
							<td width="53"><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('Director')}</td>
						</tr>
						<tr>
							<td>{t}Talent{/t}</td>
							<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('Talent')}</td>
							<td>{t}DoP{/t}</td>
							<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('DoP')}</td>
							<td>{t}Editor{/t}</td>
							<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('Editor')}</td>
						</tr>
						<tr>
							<td>{t}Talent Expenses{/t}</td>
							<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('TalentExpenses')}</td>
							<td>{t}Production Staff{/t}</td>
							<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('ProductionStaff')}</td>
							<td>{t}Props{/t}</td>
							<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('Props')}</td>
							</tr>
						<tr>
							<td>{t}Special Effects{/t}</td>
							<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('SpecialEffects')}</td>
							<td>{t}Wardrobe{/t}</td>
							<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('Wardrobe')}</td>
							<td>{t}Hair and Make-up{/t}</td>
							<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('HairMakeUp')}</td>
						</tr>
						<tr>
							<td>{t}Camera rental{/t}</td>
							<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('CameraRental')}</td>
							<td>{t}Sound{/t}</td>
							<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('Sound')}</td>
							<td>{t}Lighting{/t}</td>
							<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('Lighting')}</td>
						</tr>
						<tr>
							<td>{t}Transportation{/t}</td>
							<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('Transportation')}</td>
							<td>{t}Crew Expenses{/t}</td>
							<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('CrewExpenses')}</td>
							<td>{t}Location{/t}</td>
							<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('Location')}</td>
						</tr>
						<tr>
							<td colspan="4"></td>
							<td>{t}Others{/t}</td>
							<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('Others')}</td>
						</tr>
						<tr>
							<td colspan="5" align="right"><h3><strong>{t}Total{/t}</strong></h3></td>
							<td><h3><strong>{$oGrant->getGrants()->getCurrencySymbol()} <span id="TotalGrantAmount">{if $oGrant->getRequestedAmount() > 0}{$oGrant->getRequestedAmount()}{/if}</span></strong></h3></td>
						</tr>
					</table>
			</div>
			<div class="formFieldContainer">
				<h4>{t}Requested On{/t}</h4>
				<div class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom">
					<p class="grantsview">{$oGrant->getCreated()->getDate()|date_format:"%e %b %y"}</p>
				</div>
			</div>
			<div class="formFieldContainer">
				<h4>{t}Showreel URL{/t}</h4>
				<div class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom">
					<p class="grantsview">{if $oGrant->getParamSet()->getParam('ShowReelURL')}<a href="{$oGrant->getParamSet()->getParam('ShowReelURL')}" target="_blank">{$oGrant->getParamSet()->getParam('ShowReelURL')}</a>{else}N/A{/if}</p>
				</div>
			</div>
			<div class="formFieldContainer">
				<h4>{t}Supporting Documents{/t}</h4>
				{if $oGrant->isFileExists('GrantAssetsPath')}
					<a href="/download/grantDownloads/GrantAssets/{$oGrant->getID()}">Click Here to Download File</a>
				{else}
					No Supporting Documents Uploaded.
				{/if}
			</div>
			<a id="docs"></a>
			{if $oGrant->getStatus() == 'Approved'}
				<form id="grantsDocUpload" class="userGrantsUploadForm" name="userGrantsUploadForm" method="post" action="/account/grants/doDocsUpload" enctype="multipart/form-data">
				<fieldset>
					<legend>Upload Documents</legend>
					<div class="formFieldContainer">
						<h4>{t}Grant Approval Form{/t}</h4>
						<div>
							{if $oGrant->getParamSet()->getParam('DocumentAgreement') == 0 }
								{if $oGrant->isFileExists('DocumentAgreementPath')}
									<input type="file" name="GrantFile[UploadGrantApprovalForm]" id="UploadGrantApprovalForm" class="string" onclick="r=confirm('File Already uploaded.Do you want to reupload?'); if (r==false) { return false; } else { return true; }" />
									FILE UPLOADED SUCESSFULLY.
									<a href="/download/grantDownloads/DocumentAgreement/{$oGrant->getID()}">Click Here to Download File</a>
								{else}
									<input type="file" name="GrantFile[UploadGrantApprovalForm]" id="UploadGrantApprovalForm" class="string"/>
								{/if}
								<h5>Note : You can compress all your files in Zip format and then upload !</h5>
							{else}
								DOCUMENT VERIFIED.
								<a href="/download/grantDownloads/DocumentAgreement/{$oGrant->getID()}">Click Here to Download File</a>
							{/if}
						</div>
					</div>
					<div class="formFieldContainer">
						<h4>{t}Bank Details{/t}</h4>
						<div>
							{if $oGrant->getParamSet()->getParam('DocumentBankDetails') == 0 }
								{if $oGrant->isFileExists('DocumentBankDetailsPath')}
									<input type="file" name="GrantFile[UploadBankDetails]" id="UploadBankDetails" class="string" onclick="r=confirm('File Already uploaded.Do you want to reupload?'); if (r==false) { return false; } else { return true; }" />
									FILE UPLOADED SUCESSFULLY.
									<a href="/download/grantDownloads/DocumentBankDetails/{$oGrant->getID()}">Click Here to Download File</a>
								{else}
									<input type="file" name="GrantFile[UploadBankDetails]" id="UploadBankDetails" class="string" />
								{/if}
								<h5>Note : You can compress all your files in Zip format and then upload !</h5>
							{else}
								DOCUMENT VERIFIED.
								<a href="/download/grantDownloads/DocumentBankDetails/{$oGrant->getID()}">Click Here to Download File</a>
							{/if}
						</div>
					</div>
					<div class="formFieldContainer">
						<h4>{t}Photo ID Proof{/t}</h4>
						<div>
							{if $oGrant->getParamSet()->getParam('DocumentIdProof') == 0 }
								{if $oGrant->isFileExists('DocumentIdProofPath')}
									<input type="file" name="GrantFile[UploadPhotoIDProof]" id="UploadPhotoIDProof" class="string" onclick="r=confirm('File Already uploaded.Do you want to reupload?'); if (r==false) { return false; } else { return true; }" />
									FILE UPLOADED SUCESSFULLY.
									<a href="/download/grantDownloads/DocumentIdProof/{$oGrant->getID()}">Click Here to Download File</a>
								{else}
									<input type="file" name="GrantFile[UploadPhotoIDProof]" id="UploadPhotoIDProof" class="string" />
								{/if}
								<h5>Note : You can compress all your files in Zip format and then upload !</h5>
								
							{else}
								DOCUMENT VERIFIED.
								<a href="/download/grantDownloads/DocumentIdProof/{$oGrant->getID()}">Click Here to Download File</a>
							{/if}
						</div>
					</div>
					<div class="formFieldContainer">
						<h4>{t}Receipts{/t}</h4>
						<div>
							{if $oGrant->getParamSet()->getParam('DocumentReceipts') == 0 }
								{if $oGrant->isFileExists('DocumentReceiptsPath')}
									<input type="file" name="GrantFile[UploadReceipts]" id="UploadReceipts" class="string" onclick="r=confirm('File Already uploaded.Do you want to reupload?'); if (r==false) { return false; } else { return true; }" />
									FILE UPLOADED SUCESSFULLY.
									<a href="/download/grantDownloads/DocumentReceipts/{$oGrant->getID()}">Click Here to Download File</a>
								{else}
									<input type="file" name="GrantFile[UploadReceipts]" id="UploadReceipts" class="string" />
								{/if}
								<h5>Note : You can compress all your files in Zip format and then upload !</h5>
							{else}
								DOCUMENT VERIFIED.
								<a href="/download/grantDownloads/DocumentReceipts/{$oGrant->getID()}">Click Here to Download File</a>
							{/if}
						</div>
					</div>
					<div class="actions">
						<input type="hidden" name="UserMovieGrantID" value="{$oGrant->getID()}" />
						<input type="submit" name="DocumentsUpload" value="Upload Documents" onclick="documentsUploadUI()" />
					</div>
				</fieldset>
				</form>
			{/if}
				<br class="clearBoth">						
			</div>
		{else}
		<p>{t}No Record Found{/t}.</p>
		{/if}
	</div>
</div>
{include file=$oView->getTemplateFile('footer', 'shared')}