{include file=$oView->getTemplateFile('header', 'shared') pageTitle=''}
{include file=$oView->getTemplateFile('menu', 'shared')}
<div id="body"> 
	<div class="container">
		
		<div class="editTitle">
			<span class="imgWrap"><img class="valignMiddle" width="50" height="28" border="0" title="Event: {$oGrant->getGrants()->getSource()->getEvent()->getName()}" alt="{$oGrant->getGrants()->getSource()->getEvent()->getName()}" src="/resources/client/events/{$oGrant->getGrants()->getSource()->getEvent()->getLogoName()}.jpg"></span>
			<span class="imgWrap"><img class="valignMiddle" width="50" height="28" border="0" title="Source: {$oGrant->getGrants()->getSource()->getName()}" alt="{$oGrant->getGrants()->getSource()->getName()}" src="/resources/client/sources/{$oGrant->getGrants()->getSource()->getLogoName()}.jpg"></span>
			<h2>{t}Grant Details for - {$oGrant->getFilmTitle()}{/t}</h2>
		</div>
		
		<div class="content">
			<div class="daoAction">
				<a href="javascript:history.go(-1);" title="{t}Previous Page{/t}">
					<img src="{$themeicons}/32x32/action-back.png" alt="{t}Previous Page{/t}" class="icon" />
					{t}Previous Page{/t}
				</a>
				<a href="/grants/generatePdf/{$oGrant->getID()}" target="_blank">
					<img src="{$themeicons}/32x32/mime-application-pdf.png" alt="Generate PDF" class="icon" />
					{t}PDF{/t}
				</a>
			</div>
			<div class="clearBoth"></div>
		</div>
					
		<div class="content">
			<div class="formFieldModerator">
				<h3><strong>{t}Status{/t}</strong> : {t}{$oGrant->getStatus()}{/t}</h3>
			</div>
                        
                        <div class="formFieldModerator">
                            
                            <h4>Film Maker</h4>
                            <a href="/users/edit/{$oGrant->getUserID()}" title="{t}View users admin profile{/t}" target="_blank"> {$oGrant->getUser()->getFullname()}  </a>
                             
                        </div>
                        <div class="formFieldContainer">
				<h4>What is the main problem we need to solve?</h4>
				<div class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom">
					<p class="grantsview">{$oGrant->getParamSet()->getParam('MomindsTitle')}</p>
				</div>
			</div>    
                            
			<div class="formFieldContainer">
				<h4>What is the insight?</h4>
				<div class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom">
					<p class="grantsview">{$oGrant->getParamSet()->getParam('Insight')}</p>
				</div>
			</div>
                       
                       <div class="formFieldContainer">
				<h4>What is the creative platform?</h4>
				<div class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom">
					<p class="grantsview">{$oGrant->getFilmConcept()}</p>
				</div>
			</div>         
                        
                        <div class="formFieldContainer">
				<h4>How would you execute this creative platform for video content?</h4>
				<div class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom">
					<p class="grantsview">{$oGrant->getScript()}</p>
				</div>
			</div>        
			
                        <div class="formFieldContainer">
				<h4>Please describe 3 additional film or non-film executions that could be a part of the creative platform</h4>
				<div class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom">
					<p class="grantsview">{$oGrant->getParamSet()->getParam('ReferenceFilm1')}</p>
                                        <p class="grantsview">{$oGrant->getParamSet()->getParam('ReferenceFilm2')}</p>
                                        <p class="grantsview">{$oGrant->getParamSet()->getParam('ReferenceFilm3')}</p>
				</div>
			</div> 
                         
                        <div class="formFieldContainer">
				<h4>Please upload 3 image references that help bring the insight, idea, tonality and / or style of the creative platform to life.</h4>
				<div class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom">
                                  {if $oGrant->isFileExists('ReferenceImage1')}
				<div class="formFieldContainer">
					<h4>Reference Image 1 </h4>
						<a href="/download/grantDownloads/ReferenceImage1/{$oGrant->getID()}">Click Here</a>
					</div>
				{/if}
                                {if $oGrant->isFileExists('ReferenceImage2')}
				<div class="formFieldContainer">
					<h4>Reference Image 2 </h4>
						<a href="/download/grantDownloads/ReferenceImage2/{$oGrant->getID()}">Click Here</a>
					</div>
				{/if}
                                {if $oGrant->isFileExists('ReferenceImage3')}
				<div class="formFieldContainer">
					<h4>Reference Image 3 </h4>
						<a href="/download/grantDownloads/ReferenceImage3/{$oGrant->getID()}">Click Here</a>
					</div>
				{/if}
                                </div>
			</div>        
                                
                                
			<div class="formFieldContainer">
				<h4>User Request Details</h4>
				<table class="data">
					<thead>
						<tr>
							<th width="33%"><h4>Requested By</h4></th>
							<th width="33%"><h4>Rquested On</h4></th>
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
				<h4>Moderated Details</h4>
				<table class="data">
					<thead>
						<tr>
							<th width="33%"><h4>Moderated By</h4></th>
							<th width="33%"><h4>Granted Amount</h4></th>
							<th width="33%"><h4>Moderated On</h4></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>{if $oGrant->getModeratorID() > 0 }{$oGrant->getModerator()->getPropername()}{else} - {/if}</td>
							<td>{if $oGrant->getGrantedAmount() > 0 }{$oGrant->getGrants()->getCurrencySymbol()} {$oGrant->getGrantedAmount()}{else} - {/if}</td>
							<td>{if $oGrant->getModerated()}{$oGrant->getModerated()->getDate()|date_format:"%e %b %y"}{/if}</td>
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
							<td width="25%">
								<strong>Documents List</strong>
							</td>
							<td width="25%" align="center">
								<strong>Document Uploaded</strong>
							</td>
							<td width="25%" align="center">
								<strong>Document Verified</strong>
							</td>
							<td width="25%">
								<strong>File Download</strong>
							</td>
						</tr>
						<tr>
							<td width="25%">
								Creative Agreement Form
							</td>
							<td width="25%" align="center">
								{if $oGrant->isFileExists('DocumentAgreementPath')}
									<img src="/themes/shared/icons/accept.png" width="16" height="16" />
								{else}
									<img src="/themes/shared/icons/cancel.png" width="16" height="16" />
								{/if}
							</td>
							<td width="25%" align="center">
								{if $oGrant->getParamSet()->getParam('DocumentAgreement') == 1}
									<img src="/themes/shared/icons/accept.png" width="16" height="16" />
								{else}
									<img src="/themes/shared/icons/cancel.png" width="16" height="16" />
								{/if}
							</td>
							<td width="25%">
								{if $oGrant->isFileExists('DocumentAgreementPath')}
									<a href="/download/grantDownloads/DocumentAgreement/{$oGrant->getID()}">Click Here</a>
								{/if}
							</td>
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
            
            </div>
                                {/if}
                                                        
                                                        

			<div class="formFieldContainer">
				<h4>Moderator Comments</h4>
				<div class="ui-accordion-content ui-widget-content ui-accordion-content-active ui-corner-top ui-corner-bottom">
					<p class="grantsview">{$oGrant-> getModeratorComments()|nl2br}</p>
				</div>
			</div>
			<br class="clearBoth">
		</div>
	</div>
</div>
{include file=$oView->getTemplateFile('footer', 'shared')}