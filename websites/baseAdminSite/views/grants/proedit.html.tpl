{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Apply Grants for :: {/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}
<div id="body">
    <div class="container">
        <div class="editTitle">
            <span class="imgWrap"><img class="valignMiddle" width="50" height="28" border="0" title="Event: {$oGrant->getGrants()->getSource()->getEvent()->getName()}" alt="{$oGrant->getGrants()->getSource()->getEvent()->getName()}" src="/resources/client/events/{$oGrant->getGrants()->getSource()->getEvent()->getLogoName()}.jpg"></span>
            <span class="imgWrap"><img class="valignMiddle" width="50" height="28" border="0" title="Source: {$oGrant->getGrants()->getSource()->getName()}" alt="{$oGrant->getGrants()->getSource()->getName()}" src="/resources/client/sources/{$oGrant->getGrants()->getSource()->getLogoName()}.jpg"></span>
            <h2>{t}Idea Details for - {$oGrant->getFilmTitle()}{/t}</h2>
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
                    <a href="/users/edit/{$oGrant->getUserID()}" title="{t}View users admin profile{/t}" target="_blank"> {$oGrant->getUser()->getFullname()}  </a>

                </div>
                <br>
                <div class="formFieldContainer">
                    {if !($oGrant->getApplicationAppliedStatus())}
                        <div class="spanred">Application applied past deadline.</div>
                    {/if}
                   <h4> {if $QuestionVal} {$QuestionVal} {else} Film Concept {/if}</h4>
					<div class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom">
						<p class="grantsview">{$oGrant->getFilmConcept()}</p>
					</div>
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
                    <h4>Moderation</h4>
                    <table class="data">
                        <thead>
                            <tr>
                                <th width="33%"><h4>Moderated By</h4></th>
                        
                        <th width="33%"><h3>Status</h3></th>
                        <th width="33%"><h3>MovieID</h3></th>
                        </tr>
                        </thead>
                        <tbody>
							<tr>
								<td>{if $oGrant->getModeratorID() > 0 }{$oGrant->getModerator()->getPropername()}{else} - {/if}</td>
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
                            Ideation Agreement Form
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
