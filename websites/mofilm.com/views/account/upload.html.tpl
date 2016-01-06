{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Upload a Movie{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}
<noscript>
<meta http-equiv="refresh" content="1;url=/account/upload/uploadNojs">
</noscript>
<style>

a {
    text-decoration:underline;
    color:#00F;
    cursor:pointer;
}

#sheepItForm_controls div, #sheepItForm_controls div input {
    float:left;    
    margin-right: 10px;
}

.ui-autocomplete-category {
    font-weight: bold;
    padding: .2em .4em;
    margin: .8em 0 .2em;
    line-height: 1.5;
}

</style>
<div id="body">
	<div class="container">
		<h2>
			{t}Choose the event you are submitting your video to:{/t}
		</h2>
		<div class="floatLeft accountDetails">	
			<table>
				<tr><td> 	
						<form id="profileForm1" class="monitor" action="{$doMovieSave}" method="post" name="profileForm" accept-charset="utf-8">
							<div id="wizard" class="swMain">	
								<ul>
									<li><a href="#step-1">
											<label class="stepNumber">1</label>
											<span class="stepDesc">
												{t}Step{/t} 1<br />
												<small>{t}Event/Brand Details{/t}</small>
											</span>
										</a></li>
									<li><a href="#step-2">
											<label class="stepNumber">2</label>
											<span class="stepDesc">
												{t}Step{/t} 2<br />
												<small>{t}Upload the Video{/t}</small>
											</span>
										</a></li>
									<li><a href="#step-3">
											<label class="stepNumber">3</label>
											<span class="stepDesc">
												{t}Step{/t} 3<br />
												<small>{t}Video Details{/t}</small>
											</span>                   
										</a></li>
								</ul>			
								<div id="step-1">
									<h2>{t}Choose the Event/Brand{/t}</h2>	
									<br />
									<table align="center">
										<tr>
											<td>{t}Choose the Event{/t}</td>
											<td>
												<select id ="eventUpload" name="EventID">
													<option value="">{t}Select event{/t}</option>
													{foreach $eventsall as $oEvent}
														<option value="{$oEvent->getID()}" {if $eventID  == $oEvent->getID()} selected="selected"{/if}>{$oEvent->getName()}</option>
													{/foreach}	
												</select>	
											</td>
											<td align="left"><span id="msg_event" style="color:red"></span></td>
										</tr>
										<tr>
											<td>{t}Choose the Brand{/t}</td>
											<td>					
												<select id="sourceUpload" name="sourceID">
												</select>	
											</td>
											<td align="left"><span id="msg_source" style="color:red"></span></td>
										</tr>
										<tr>
											<td>{t}Agree to Mofilm{/t} 
												<br />
												<a target="_blank" href="{t}http://www.mofilm.com/info/uploadTerms.html{/t}">{t}Terms and Conditions{/t}</a> 
											</td>
											<td><input type="checkbox" id="tnc"></td>
											<td align="left"><span id="msg_agree" style="color:red"></span></td>
										</tr>	
										<tr>
										<br />
										</tr>									
									</table>
									<br />
								</div>        
								<div id="step-2">
									<h2 >{t}Upload the video{/t}</h2>
									<br />
									<div align="center">
										<input type="file" id="FileUpload" name="Files" class="long"/>
										<input type="hidden" id="fileNameStored" name="fileName" />
										<input type="hidden" id="uploadStatus" />									
									<p>
									<span id="msg_filename" style="color:blue"></span>
									</p>

									<p>{t}Maximum Size : 500MB, Supported formats : mov,wmv,avi,mp4,mpg,m4v{/t}</p>
									<p> <a href="{t}http://mofilm.com/competitions/faq.html{/t}" target="_blank">{t}HELP{/t}</a></p>
									</div>
								</div>
								<div id="step-3">
									<div id="userFormAccordion">
										<h3><a href="#">{t}Movie Details{/t}</a></h3>
										<div>
											<div class="formFieldContainer">
												<h4>{t}Title{/t}</h4>
												<p>
													<input type="text" name="Title" value="" class="long" id="movieTitle"/>
													<span id="msg_title" style="color:red"></span>
												</p>
											</div>
											<div class="formFieldContainer">
												<h4>{t}Description{/t}</h4>
												<p>
													<textarea name="Description" rows="4" cols="60" class="long" id="movieDesc"></textarea>
													<span id="msg_desc" style="color:red"></span>
												</p>
											</div>
											<div class="formFieldContainer">
												<h4>{t}Tags{/t}</h4>
												<div class="ui-widget">
													{assign var=i value=1}
													{foreach $newGenres as $oTags}
													    <div style="width:570px; height:25px; padding:4px;"><strong>{$oTags@key}</strong></div>
														<div style="padding:4px;width:570px; overflow:auto;">
															{foreach $oTags as $oTag}
																<div style="float:left; width:185px;">
																	{if $oTag->getCategory() == "Industry"}
																		<input class="industry" type="checkbox" name="Tags[]" value="{$oTag->getID()}" />{$oTag->getName()}
																	{else}
																		<input type="checkbox" name="Tags[]" value="{$oTag->getID()}" />{$oTag->getName()}
																	{/if}
																</div>
															{/foreach}
														</div>
													{/foreach}
												</div>
											</div>
										</div>
										<h3><a href="#">{t}Mofilmmusic License (Pre-cleared){/t}</a></h3>
										<div class="tableContainer">
											{if count($oLicenseSet) > 0 }
											<table border="0" cellpadding="0" cellspacing="0" width="100%" class="scrollTable" id="licenseContent">
												<thead class="fixedHeader">
													<tr>
														<th style="width: 105px;">{t}License ID{/t}</th>
														<th style="width: 275px;">{t}Track Name{/t}</th>
														<th style="width: 40px;">{t}Status{/t}</th>
														<th style="width: 100px;">{t}Select{/t}</th>
													</tr>	
												</thead>	
												<tbody class="scrollContent">
													{foreach $oLicenseSet as $oLicense}
														<tr>
														<td style="width: 105px;">{$oLicense->getLicenseID()}</td>
														<td style="width: 275px;">{$oLicense->getTrackName()}</td>
														<td style="width: 40px;">{if $oLicense->isValidLicense()}Valid{else}Expired{/if}</td>
												<td style="width: 100px;"><input type="checkbox" name="LicenseID[]" value="{$oLicense->getLicenseID()}"/></td>
														</tr>	
													{/foreach}
												</tbody>
											</table>
											{else}
												<p>{t}If you have used a MOMUSIC pre-cleared track .Please go to the section below (Alternate selection / others) and type MOMUSIC pre-cleared track{/t}</p>
											{/if}	
										</div>
										
										<h3><a href="#">{t}Alternate selection / Original Composition / Others{/t}</a></h3>
										<div>																						
											<div class="formFieldContainer">
												<h4>{t}Describe your license{/t}</h4>
												<p>
													<textarea name="customLicense" id="cLicense"  rows="4" cols="60" class="long"></textarea>
												</p>
											</div>
										</div>	
										<!--  -->			
										<h3><a href="#">{t}Give credit to your creative team !{/t}</a></h3>
										<div>
											<table id="contributors" class="data">
												<thead>
													<tr>
														<th>#</th>
														<th>{t}Contributor Name{/t}</th>
														<th>{t}Role{/t}</th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													{*foreach $oMovie->getContributorSet() as $oContributorMap}	
													{if $oModel->getValidUser($oContributorMap->getContributor()->getName())}	
													<tr>
														<td><input type="hidden" name="Contributors[{$index = $oContributorMap@iteration}{$index}][ID]" value="{$oContributorMap->getContributor()->getID()}" /><span class="recordNumber">{$index}</span></td>
														<td><input placeholder="name" type="text" class="contributorUser string" name="Contributors[{$index}][Name]" value="{$oModel->getUserName($oContributorMap->getContributor()->getName())}" /></td>
														<td><div class="ui-widget"><input type="text" class="contributorRole small" name="Contributors[{$index}][Role]" value="{$oContributorMap->getRole()->getDescription()}" /></div></td>
														<td><input type="checkbox" name="Contributors[{$index}][Remove]" value="1"  /></td>
													</tr>
													{/if}
													{/foreach*}
													<tr>
														<td><input type="hidden" name="Contributors[{$index+1}][ID]" value="0" /><span class="recordNumber">{$index+1}</span></td>
														<td><input type="text" class="contributorUser string" name="Contributors[{$index+1}][Name]" value="" /></td>
														<td><div class="ui-widget"><input type="text" class="contributorRole small" name="Contributors[{$index+1}][Role]" value="" /></div></td>
														<td><input class="addRemoveControl" type="checkbox" name="Contributors[{$index+1}][Remove]" value="1"  title="{t}Tick box to remove contributor{/t}" /></td>
														<td><input type="hidden" name="Contributors[{$index+1}][roleID]" value="0" /></td>

													</tr>
												</tbody>
											</table>
											<div class="controls hidden">
												<div class="floatRight">
													<div class="addContributor formIcon ui-state-default floatLeft" title="{t}Add New Contributor{/t}"><span class="ui-icon ui-icon-plusthick"></span></div>
													<div class="removeContributor formIcon ui-state-default floatLeft" title="{t}Remove Last Contributor{/t}"><span class="ui-icon ui-icon-minusthick"></span></div>
												</div>
												<div class="clearBoth"></div>
											</div>
										</div>
										<!-- -->
									</div>	
								</div>
							</div>
						</form>								
					</td></tr>
			</table>
		</div>

		<div class="floatLeft accountStats">
			<p>{t}Thanks for uploading your video, by entering a contest you're becoming a true member of the MOFILM community {/t}</p>
			<p>{t}Uploading your video may take several minutes, depending on file size and your Internet connection. A 5MB video will take at least 5 minutes to upload using a 512 kbps ADSL connection. Please be patient!{/t}</p>
			<p>{t}After the movie is uploaded it takes up to 24hrs for the video to get encoded .Kindly wait for the confirmation email.{/t}</p>
			<p>{t}If you have any questions please email <a href="mailto:support@mofilm.com">support@mofilm.com</a>{/t}</p>
			<p>{t}If you have trouble uploading try our basic uploader <a href="/account/upload/plupload"><strong>UPLOAD</strong></a>{/t}</p>
			<p style="font-size: 17px;">{t}<b>Good Luck!</b>{/t}</p>
		</div>
		<br class="clearBoth">
	</div>				
</div>	
<script type="text/javascript">
	<!--
	var availableRoles = {$availableRoles};
	//var availableUsers = {*$availableUsers*};
	//-->
</script>			
{include file=$oView->getTemplateFile('footer', 'shared')}
