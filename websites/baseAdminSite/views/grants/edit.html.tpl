{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Apply Grants for :: {/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}
<div id="body">
    <div class="container">
        <div class="editTitle">
            <span class="imgWrap"><img class="valignMiddle" width="50" height="28" border="0" title="Event: {$oGrant->getGrants()->getSource()->getEvent()->getName()}" alt="{$oGrant->getGrants()->getSource()->getEvent()->getName()}" src="/resources/client/events/{$oGrant->getGrants()->getSource()->getEvent()->getLogoName()}.jpg"></span>
            <span class="imgWrap"><img class="valignMiddle" width="50" height="28" border="0" title="Source: {$oGrant->getGrants()->getSource()->getName()}" alt="{$oGrant->getGrants()->getSource()->getName()}" src="/resources/client/sources/{$oGrant->getGrants()->getSource()->getLogoName()}.jpg"></span>
            <h2>{t}Grant Details for - {$oGrant->getFilmTitle()}{/t}</h2>
        </div>
        <form id="grantsApprovalForm" class="userGrantsApprovalForm" name="grantsApprovalForm" method="post" action="/grants/doDocsUpload"  enctype="multipart/form-data">

            <div class="content">	
                <div class="daoAction">
                    <a href="javascript:history.go(-1);" title="{t}Previous Page{/t}">
                        <img src="{$themeicons}/32x32/action-back.png" alt="{t}Previous Page{/t}" class="icon" />
                        {t}Previous Page{/t}
                    </a>
                    <a href="javascript:history.go(-1);" title="{t}Cancel{/t}">
                        <img src="{$themeicons}/32x32/action-cancel.png" alt="{t}Cancel{/t}" class="icon" />
                        {t}Cancel{/t}
                    </a>
                    <button class="reset" value="Reset" name="Reset" type="reset">
                        <img class="icon" alt="Undo changes" src="/themes/mofilm/images/icons/32x32/action-undo.png">
                        {t}Reset{/t}
                    </button>
                    <a href="/grants/generatePdf/{$oGrant->getID()}" target="_blank">
                        <img src="{$themeicons}/32x32/mime-application-pdf.png" alt="Generate PDF" class="icon" />
                        {t}PDF{/t}
                    </a>
                    <button type="button" name="submitGrant" value="Save" title="{t}Save{/t}" id="userMovieGrantsSubmit">
                        <img src="{$themeicons}/32x32/action-do-edit-object.png" alt="{t}Save{/t}" class="icon" />
                        {t}Save Changes{/t}
                    </button>
                    <a href="/grants/sendEmail/{$oGrant->getID()}?height=450&width=900&modal=false" title="{t}Send Email to Film Maker{/t}" id="userMovieGrantsSentEmail" class="thickbox">
                        <img src="/themes/shared/icons/email.png" alt="{t}Send Email{/t}" class="icon" />
                        {t}Send Email{/t}
                    </a>
                </div>
                <div class="clearBoth"></div>
            </div>
            <div class="content">
                <div class="formFieldModerator">

                    <h4>Film Maker</h4>
                    <a href="{system::getConfig()->getParam('mofilm', 'platformUri')->getParamValue()}{'users/'}{$oGrant->getUserID()}{'?token='}{$accessToken}" title="{t}View users admin profile{/t}" target="_blank"> {$oGrant->getUser()->getFullname()}  </a>

                </div>
                <br>
                <div class="formFieldContainer">
                    {if !($oGrant->getApplicationAppliedStatus())}
                        <div class="spanred">Application applied past deadline.</div>
                    {/if}
                    <h4>Film Concept</h4>
                    <div class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom">
                        <p class="grantsview">{$oGrant->getFilmConcept()}</p>
                    </div>
                </div>

                <div class="formFieldContainer">
                    <h4>Usage of Grants</h4>
                    <div class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom">
                        <p class="grantsview">{$oGrant->getUsageOfGrants()}</p>
                    </div>
                </div>

                <div class="formFieldContainer">
                    <h4>Script</h4>
                    <div class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom">
                        <p class="grantsview">{$oGrant->getScript()}</p>
                    </div>
                </div>
                <div class="formFieldContainer">
                    <h4>Requested Amount</h4>
                    <table width="100%" class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom" style="padding-top:20px; padding-left: 40px;">
                        <tr>
                            <td width="33">Script writer</td>
                            <td width="93"><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('ScriptWriter')}</td>
                            <td width="33">Producer</td>
                            <td width="93"><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('Producer')}</td>
                            <td width="33">Director</td>
                            <td width="53"><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('Director')}</td>
                        </tr>
                        <tr>
                            <td>Talent</td>
                            <td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('Talent')}</td>
                            <td>DoP</td>
                            <td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('DoP')}</td>
                            <td>Editor</td>
                            <td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('Editor')}</td>
                        </tr>
                        <tr>
                            <td>Talent Expenses</td>
                            <td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('TalentExpenses')}</td>
                            <td>Production Staff</td>
                            <td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('ProductionStaff')}</td>
                            <td>Props</td>
                            <td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('Props')}</td>
                        </tr>
                        <tr>
                            <td>Special Effects</td>
                            <td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('SpecialEffects')}</td>
                            <td>Wardrobe</td>
                            <td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('Wardrobe')}</td>
                            <td>Hair & Make-up</td>
                            <td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('HairMakeUp')}</td>
                        </tr>
                        <tr>
                            <td>Camera rental</td>
                            <td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('CameraRental')}</td>
                            <td>Sound</td>
                            <td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('Sound')}</td>
                            <td>Lighting</td>
                            <td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('Lighting')}</td>
                        </tr>
                        <tr>
                            <td>Transportation</td>
                            <td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('Transportation')}</td>
                            <td>Crew Expenses</td>
                            <td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('CrewExpenses')}</td>
                            <td>Location</td>
                            <td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('Location')}</td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td>Others</td>
                            <td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> {$oGrant->getParamSet()->getParam('Others')}</td>
                        </tr>
                        <tr>
                            <td colspan="5" align="right"><h3><strong>Total</strong></h3></td>
                            <td><h3><strong>{$oGrant->getGrants()->getCurrencySymbol()} <span id="TotalGrantAmount">{if $oGrant->getRequestedAmount() > 0}{$oGrant->getRequestedAmount()}{/if}</span></strong></h3></td>
                        </tr>
                    </table>
                </div>
                <div class="formFieldContainer">
                    <h4>{t}Showreel URL{/t}</h4>
                    <div class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom">
                        <p class="grantsview">
                            {if $oGrant->getParamSet()->getParam('ShowReelURL')}
                                <a href="{$oGrant->getParamSet()->getParam('ShowReelURL')}" target="_blank">
                                    {$oGrant->getParamSet()->getParam('ShowReelURL')}
                                </a>
                            {else}
                                N/A
                            {/if}
                        </p>
                    </div>
                </div>
                <div class="formFieldContainer">
                    <h4>User Request Details</h4>
                    <table class="data">
                        <thead>
                            <tr>
                                <th width="33%"><h3>Requested By</h3></th>
                        <th width="33%"><h3>Rquested On</h3></th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{$oGrant->getUser()->getPropername()}</td>
                                <td>{$oGrant->getCreated()->getDate()|date_format:"%e %b %y"}</td>
                            </tr>
                        </tbody>

                    </table>
                </div>

                <div class="formFieldContainer">
                    <h4>Grant Stats</h4>
                    <input type="hidden" value="{$oGrant->getGrants()->getTotalGrants()}" id="GrantsAvailable">
                    <input type="hidden" value="{$inGrantsDisbursed}" id="GrantDispersed">
                    <input type="hidden" value="{$bufferGrant}" id="bufferGrant">
                    <div class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom">
                        <p class="grantsview">
                            <strong>Total Grants Available :</strong> {$oGrant->getGrants()->getCurrencySymbol()} {$oGrant->getGrants()->getTotalGrants()}
                            <br class="clearBoth">
                            <strong>Total Grants Disbursed :</strong> {$oGrant->getGrants()->getCurrencySymbol()} {$inGrantsDisbursed}
                        </p>
                    </div>
                </div>

                <div class="formFieldContainer">
                    <h4>Moderation</h4>
                    <table class="data">
                        <thead>
                            <tr>
                                <th width="33%"><h4>Moderated By</h4></th>
                        <th width="33%"><h3>Granted Amount</h3></th>
                        <th width="33%"><h3>Status</h3></th>
                        <th width="33%"><h3>MovieID</h3></th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{if $oGrant->getModeratorID() > 0 }{$oGrant->getModerator()->getPropername()}{else} - {/if}</td>
                                <td>
                                    {$oGrant->getGrants()-> getCurrencySymbol()}
                                    {if $oGrant->getStatus()}
                                    <input type="hidden" name="currentStatus" id="currentStatus" value="{$oGrant->getStatus()}" >
                                    {/if}
                                    <input class="small required" type="text" name="GrantedAmount" id="GrantedAmount" value="{if $oGrant->getGrantedAmount() > 0}{$oGrant->getGrantedAmount()}{/if}" {if $oGrant->getStatus() !== 'Approved'}readonly="readonly"{/if}/>
                                    <input type="hidden" id="existingGrantAmount" value="{$oGrant->getGrantedAmount()}" name="existingGrantAmount" />
                                </td>
                                <td>
                                    <select name="GrantedStatus" id="GrantedStatus">
                                        <option value="Pending" {if $oGrant->getStatus() == 'Pending'}selected{/if}>Pending</option>
                                        <option value="Approved"{if $oGrant->getStatus() == 'Approved'}selected{/if}>Approved</option>
                                        <option value="Rejected"{if $oGrant->getStatus() == 'Rejected'}selected{/if}>Rejected</option>
                                    </select>
                                </td>
                                <td>
                                    <input class="small required" type="text" name="MovieID" id="MovieID" value="{$oGrant->getMovieID()}" />
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </div>
                {if $oGrant->isFileExists('GrantAssetsPath')}
                    <div class="formFieldContainer">
                        <h4>Supporting Documents</h4>
                        <a href="/download/grantDownloads/GrantAssets/{$oGrant->getID()}">Click Here</a>
                    </div>
                </div>
            {/if}
            <div class="formFieldContainer">

                <h4>Documents Received</h4>
                <table width="100%" class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom" style="padding:20px;">
                    <tr>
                        <td width="20%">
                            <strong>Documents List</strong>
                        </td>
                        <td width="20%" align="center">
                            <strong>Document Uploaded</strong>
                        </td>
                        <td width="20%" align="center">
                            <strong>Document Verified</strong>
                        </td>
                        <td width="20%">
                            <strong>File Download</strong>
                        </td>
                        <td width="20%">
                            <strong>File Upload</strong>
                        </td>
                    </tr>
                    <tr>
                        <td width="20%">
                            Grant Approval Form
                        </td>
                        <td width="20%" align="center">
                            {if $oGrant->isFileExists('DocumentAgreementPath')}
                                <img src="/themes/shared/icons/accept.png" width="16" height="16" />
                            {else}
                                <img src="/themes/shared/icons/cancel.png" width="16" height="16" />
                            {/if}
                        </td>
                        <td width="20%" align="center">
                            {if $oGrant->isFileExists('DocumentAgreementPath')}
                                <input id="documentAgreement" type="checkbox" name="DocumentAgreement" value="1" {if $oGrant->getParamSet()->getParam('DocumentAgreement') == 1}checked{/if} />
                            {else}
                                <input id="documentAgreement" type="checkbox" name="DocumentAgreement" value="1" disabled />
                            {/if}

                        </td>
                        <td width="20%" >
                            {if $oGrant->isFileExists('DocumentAgreementPath')}
                                <a href="/download/grantDownloads/DocumentAgreement/{$oGrant->getID()}">Click Here</a>
                            {else}
                                -
                            {/if}
                        </td>
                        <td width="20%">  
                            <input type="file" name="GrantFile[UploadGrantApprovalForm]" id="UploadGrantApprovalForm" class="string"/>				
                        </td>
                    </tr>
                    <tr>
                        <td width="20%">
                            Bank Details
                        </td>
                        <td width="20%" align="center">
                            {if $oGrant->isFileExists('DocumentBankDetailsPath')}
                                <img src="/themes/shared/icons/accept.png" width="16" height="16" />
                            {else}
                                <img src="/themes/shared/icons/cancel.png" width="16" height="16" />
                            {/if}
                        </td>
                        <td width="20%" align="center">
                            {if $oGrant->isFileExists('DocumentBankDetailsPath')}
                                <input id="documentBankDetails" type="checkbox" name="DocumentBankDetails" value="1" {if $oGrant->getParamSet()->getParam('DocumentBankDetails') == 1}checked{/if} />
                            {else}
                                <input id="documentBankDetails" type="checkbox" name="DocumentBankDetails" value="1" disabled />
                            {/if}

                        </td>
                        <td width="20%">
                            {if $oGrant->isFileExists('DocumentBankDetailsPath')}
                                <a href="/download/grantDownloads/DocumentBankDetails/{$oGrant->getID()}">Click Here</a>        
                            {else}
                                -
                            {/if}
                        </td>
                        <td width="20%">
                            <input type="file" name="GrantFile[UploadBankDetails]" id="UploadBankDetails" class="string" />
                        </td>
                    </tr>
                    <tr>
                        <td width="20%">
                            Photo ID Proof
                        </td>
                        <td width="20%" align="center">
                            {if $oGrant->isFileExists('DocumentIdProofPath')}
                                <img src="/themes/shared/icons/accept.png" width="16" height="16" />
                            {else}
                                <img src="/themes/shared/icons/cancel.png" width="16" height="16" />
                            {/if}
                        </td>
                        <td width="20%" align="center">
                            {if $oGrant->isFileExists('DocumentIdProofPath')}
                                <input id="documentIdProof" type="checkbox" name="DocumentIdProof" value="1" {if $oGrant->getParamSet()->getParam('DocumentIdProof') == 1}checked{/if} />
                            {else}
                                <input id="documentIdProof" type="checkbox" name="DocumentIdProof" value="1" disabled />
                            {/if}

                        </td>
                        <td width="20%">
                            {if $oGrant->isFileExists('DocumentIdProofPath')}
                                <a href="/download/grantDownloads/DocumentIdProof/{$oGrant->getID()}">Click Here</a>
                            {else}
                                -
                            {/if}
                        </td>
                        <td width="20%">
                            <input type="file" name="GrantFile[UploadPhotoIDProof]" id="UploadPhotoIDProof" class="string" />

                        </td>
                    </tr>
                    <tr>
                        <td width="20%">
                            Receipts
                        </td>
                        <td width="20%" align="center">
                            {if $oGrant->isFileExists('DocumentReceiptsPath')}
                                <img src="/themes/shared/icons/accept.png" width="16" height="16" />
                            {else}
                                <img src="/themes/shared/icons/cancel.png" width="16" height="16" />
                            {/if}
                        </td>
                        <td width="20%" align="center">
                            {if $oGrant->isFileExists('DocumentReceiptsPath')}
                                <input id="documentReceipts" type="checkbox" name="DocumentReceipts" value="1" {if $oGrant->getParamSet()->getParam('DocumentReceipts') == 1}checked{/if} />
                            {else}
                                <input id="documentReceipts" type="checkbox" name="DocumentReceipts" value="1" disabled />
                            {/if}

                        </td>
                        <td width="20%">
                            {if $oGrant->isFileExists('DocumentReceiptsPath')}
                                <a href="/download/grantDownloads/DocumentReceipts/{$oGrant->getID()}">Click Here</a>
                            {else}
                                -   
                            {/if}
                        </td>
                        <td width="20%">
                            <input type="file" name="GrantFile[UploadReceipts]" id="UploadReceipts" class="string" />        
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="right"><input type="button" name="grantDocSubmitButton" id="grantDocSubmitButton" style="margin-top:10px;cursor: pointer;" value="Submit Document"/></td>
                    </tr>
                </table>
            </div>
                        
            {if $oController->hasAuthority('canRateVideos')}
            <h3>Ratings</h3>
            				<div class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom">
            
                {*assign var=oUsrRating value=$oMovie->getUserRating($oUser->getID())*}
                <div id="mofilmAverageRating" class="floatLeft spacer">
                    <strong>{t}Avg Rating:{/t}</strong> (<span id="mofilmGrantAverageRatingCount">{$RatingCount}</span> {t}ratings{/t})<br />
                    <div id="mofilmGrantAverageRating">
                        {for $i=0; $i<=10; $i++}
                            <input type="radio" name="Rating" value="{$i}" {if $i == $avgRating}checked="checked"{/if} />
                        {/for}
                    </div>
                </div>

                <div id="GrantRatingForm" class="floatRight spacer">
                    <strong>{t}Your Rating:{/t}</strong><br />
                    <div id="mofilmGrantRating">
                        {for $i=0; $i<=10; $i++}
                            <input type="radio" name="Rating" value="{$i}" {if $i == $userRating}checked="checked"{/if} />
                        {/for}
                    </div>
                </div>
                                <br class="clearBoth">

                                <div class="formFieldContainer">
                                    
                                    <h3>Rating History </h3>
                                    <table class="data" border="1">
                                        <tr>
                                            <th>User</th>
                                            <th>Rating</th>
                                        </tr>
                                    {foreach $ratingList as $oRating}
                                        <tr>   
                                            <td>{mofilmUserManager::getInstanceByID($oRating->getUserID())->getFullname()}</td>
                                    <td>{$oRating->getRating()}</td>
                                        </tr>  
                                    {/foreach}    
                                </table>
                                    </div>
            {/if}
            </div>
            <div class="formFieldContainer">
                <h4>Comments</h4><span class="spanred">"Comments for internal purpose only."</span>
                <div class="">

                    <textarea name="ModeratorComments" id="ModeratorComments" cols="120" rows="10">{$oGrant->getModeratorComments()|regex_replace:"/(<br>|<br [^>]*>|<\\/br>)/":""}</textarea>
                </div>
            </div>
                
                


            <div>
                <input type="hidden" name="GrantID" id="GrantID" value="{$oGrant->getID()}" />
            </div>
            <br class="clearBoth">
            <div class="note">
                <span class="spanred"><strong>NOTE : </strong></span>
                <p>If the <strong>STATUS</strong> is <strong>Pending</strong>, the record will be saved.</p>
                <p>If the <strong>STATUS</strong> is <strong>Approved</strong> or <strong>Rejected</strong>, the record will be saved and appropriate email will be sent to the applicant.</p>
                <p><strong>Granted Amount</strong> can be entered and saved only if the <strong>STATUS</strong> is <strong>Approved</strong>.</p>

            </div>

            <div class="formFieldContainer">
                <h4>Applied Grants stats for the Current Contest</h4>
                <div class="note">
                    {foreach $oResults as $oResult}
                        {if $oResult->getGrants()->getID() !== $oGrant->getGrants()->getID()}
                            <p class="grantsview">
                                <strong>{$oResult->getGrants()->getSource()->getName()}</strong> - 
                                <strong>{$oResult->getStatus()}</strong>
                                {if $oResult->getStatus() == 'Approved'}
                                    - <strong>{$oResult->getGrants()->getCurrencySymbol()}{$oResult->getGrantedAmount()}</strong>
                                {/if}
                            </p>
                        {/if}
                    {/foreach}
                </div>
            </div>

            {if $oGrant->getMessagesToFilmmaker()}
                <div class="formFieldContainer">
                    <h4>Message To Filmmaker</h4>
                    <div class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom">
                        <p class="grantsviewMessage">{$oGrant->getMessagesToFilmmaker()}</p>
                    </div>
                </div>
            {/if}

    </div>
    <br class="clearBoth">
    </form>
</div>
</div>
{include file=$oView->getTemplateFile('footer', 'shared')}