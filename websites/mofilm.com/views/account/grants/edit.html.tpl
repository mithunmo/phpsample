{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Edit Grants Appication{/t} '}
{include file=$oView->getTemplateFile('menu', 'shared')}
<div id="body">
	<div class="container">
		{if $oGrant->getID() > 0 }
		<div>
				<h2>
					<div>{t}Edit Grants For{/t} - {$oGrant->getGrants()->getSource()->getEvent()->getName()} : {$oGrant->getGrants()->getSource()->getName()}</div>
				</h2>

				<div class="grantsLogoDisplay">
					<div style="display:inline"><strong>{t}Apply before{/t} </strong>{$oGrant->getGrants()->getEndDate()->getDate()|date_format:"%e %b , %Y"}</div>
				</div>
				{if !($oGrant->getApplicationAppliedStatus())}
					<div class="spanred">
					    The deadline for production grant applications for this contest has now passed. However, we sometimes have additional money become available if a granted filmmaker is unable to submit to the contest. So please complete the application form but understand that at this stage it might not be read by an account manager.
					</div>
				{/if}

				<form id="grantsApplyForm" class="userGrantsApplyForm" name="userGrantsForm" method="post" action="/account/grants/doEdit" enctype="multipart/form-data">
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
							<button type="submit" name="submit" value="Save" title="{t}Save{/t}" class="userMovieGrantsSubmitButton" id="userMovieGrantsSubmit">
								<img src="{$themeicons}/32x32/action-do-edit-object.png" alt="{t}Save{/t}" class="icon" />
								{t}Save Changes{/t}
							</button>
						</div>
						<div class="clearBoth"></div>
					</div>

					<div class="content">
						<p class="grantsview">
						<span><strong>{t}NOTE{/t} : </strong></span>
							{t}If you have multiple concepts, you can enter all of them in this form{/t}.
						</p>
					</div>
					
					<div class="content">
						<p class="grantsview">
							{t}Before starting your entry, we recommend reading our guide on how to create a successful MOFILM production grant application over on the MOFILM Blog{/t} : 
							{if $lang == 'zh'}
								<a href="http://www.mofilm.cn/mofilm-production-grants/" target="_blank">
									http://www.mofilm.cn/mofilm-production-grants/
								</a>
							{else}
								<a href="http://www.mofilm.com/blog/2012/11/02/mofilm-production-grants/" target="_blank">
									http://www.mofilm.com/blog/2012/11/02/mofilm-production-grants/
								</a>
							{/if}
							<br /><br />
							{t}{$oGrant->getGrants()->getDescription()|nl2br}{/t}
						</p>
					</div>
					
					<div class="content">
					<div class="formFieldContainer">
						<h4>{t}Please describe the concept of your film{/t} <span class="spanred"><b>*</b></span></h4>
						<p><textarea class="extralong required string" name="FilmConcept" cols="70" rows="10" />{$oGrant->getFilmConcept()}</textarea></p>
					</div>
					<div class="formFieldContainer">
						<h4>{t}Title of your working film{/t} <span class="spanred"><b>*</b></span></h4>
						<p><input class="long required string" type="text" name="FilmTitle" value="{$oGrant->getFilmTitle()}" /></p>
					</div>
					{*
					<div class="formFieldContainer">
						<h4>Film Duration</h4>
						<p><input class="small" type="text" name="Duration" value="" /></p>
					</div>
					*}
					<div class="formFieldContainer">
						<h4>{t}Proposed use of grant funding{/t} <span class="spanred"><b>*</b></span></h4>
						<p><textarea class="extralong required string" name="UsageOfGrants" cols="70" rows="10" />{$oGrant->getUsageOfGrants()}</textarea></p>
					</div>
					<div class="formFieldContainer">
						<fieldset style="width: 872px;">
							<legend>{t}Requested Amount{/t}</legend>
							<table width="100%" cellpadding="2" cellspacing="2">
							    <tr>
								<td>{t}Script writer{/t}</td>
								<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="ScriptWriterAmount" id="ScriptWriterAmount" value="{$oGrant->getParamSet()->getParam('ScriptWriter')}" /></td>
								<td>{t}Producer{/t}</td>
								<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="ProducerAmount" id="ProducerAmount" value="{$oGrant->getParamSet()->getParam('Producer')}" /></td>
								<td>{t}Director{/t}</td>
								<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="DirectorAmount" id="DirectorAmount" value="{$oGrant->getParamSet()->getParam('Director')}" /></td>
							    </tr>
							    <tr>
								<td>{t}Talent{/t}</td>
								<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="TalentAmount" id="TalentAmount" value="{$oGrant->getParamSet()->getParam('Talent')}" /></td>
								<td>{t}DoP{/t}</td>
								<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="DoPAmount" id="DoPAmount" value="{$oGrant->getParamSet()->getParam('DoP')}" /></td>
								<td>{t}Editor{/t}</td>
								<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="EditorAmount" id="EditorAmount" value="{$oGrant->getParamSet()->getParam('Editor')}" /></td>
							    </tr>
							    <tr>
								<td>{t}Talent Expenses{/t}</td>
								<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="TalentExpensesAmount" id="TalentExpensesAmount" value="{$oGrant->getParamSet()->getParam('TalentExpenses')}" /></td>
								<td>{t}Production Staff{/t}</td>
								<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="ProductionStaffAmount" id="ProductionStaffAmount" value="{$oGrant->getParamSet()->getParam('ProductionStaff')}" /></td>
								<td>{t}Props{/t}</td>
								<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="PropsAmount" id="PropsAmount" value="{$oGrant->getParamSet()->getParam('Props')}" /></td>
							    </tr>
							    <tr>
								<td>{t}Special Effects{/t}</td>
								<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="SpecialEffectsAmount" id="SpecialEffectsAmount" value="{$oGrant->getParamSet()->getParam('SpecialEffects')}" /></td>
								<td>{t}Wardrobe{/t}</td>
								<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="WardrobeAmount" id="WardrobeAmount" value="{$oGrant->getParamSet()->getParam('Wardrobe')}" /></td>
								<td>{t}Hair & Make-up{/t}</td>
								<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="HairMakeUpAmount" id="HairMakeUpAmount" value="{$oGrant->getParamSet()->getParam('HairMakeUp')}" /></td>
							    </tr>
							    <tr>
								<td>{t}Camera rental{/t}</td>
								<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="CameraRentalAmount" id="CameraRentalAmount" value="{$oGrant->getParamSet()->getParam('CameraRental')}" /></td>
								<td>{t}Sound{/t}</td>
								<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="SoundAmount" id="SoundAmount" value="{$oGrant->getParamSet()->getParam('Sound')}" /></td>
								<td>{t}Lighting{/t}</td>
								<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="LightingAmount" id="LightingAmount" value="{$oGrant->getParamSet()->getParam('Lighting')}" /></td>
							    </tr>
							    <tr>
								<td>{t}Transportation{/t}</td>
								<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="TransportationAmount" id="TransportationAmount" value="{$oGrant->getParamSet()->getParam('Transportation')}" /></td>
								<td>{t}Crew Expenses{/t}</td>
								<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="CrewExpensesAmount" id="CrewExpensesAmount" value="{$oGrant->getParamSet()->getParam('CrewExpenses')}" /></td>
								<td>{t}Location{/t}</td>
								<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="LocationAmount" id="LocationAmount" value="{$oGrant->getParamSet()->getParam('Location')}" /></td>
							    </tr>
							    <tr>
								<td colspan="4"></td>
								<td>{t}Others{/t}</td>
								<td><b>{$oGrant->getGrants()->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="OthersAmount" id="OthersAmount" value="{$oGrant->getParamSet()->getParam('Others')}" /></td>
							    </tr>
							    <tr>
								<td colspan="5" align="right"><h3><strong>{t}Total{/t}</strong></h3></td>
								<td><h3><strong>{$oGrant->getGrants()->getCurrencySymbol()} <span id="TotalGrantAmount">{if $oGrant->getRequestedAmount() > 0}{$oGrant->getRequestedAmount()}{/if}</span></strong></h3></td>
							    </tr>
							    <tr>
								<td colspan="4"></td>
								<td colspan="2">{t}Minimum amount to request is USD 500{/t}</td>
							    </tr>
							</table>
						</fieldset>
					</div>
					<div class="formFieldContainer">
						<h4>
							{if $lang == 'zh'}
								{t}If you have a script or other supporting information then please include it here, or you can send through additional material to{/t} 
							{else}
								{t}If you have a script or other supporting information then please include it here, or{/t} <br />{t}you can send through additional material to{/t} 
							{/if}
							<a href="mailto:productiongrant@mofilm.com">productiongrant@mofilm.com</a>
						</h4>
						<p><textarea class="extralong string" name="Script" cols="70" rows="10" />{$oGrant->getScript()}</textarea></p>
					</div>
					</div>
					<div class="content">
						<div class="formFieldContainer">
							<h4>{t}Supporting Documents{/t}</h4>
							{if $oGrant->isFileExists('GrantAssetsPath')}
								<a href="/download/grantDownloads/GrantAssets/{$oGrant->getID()}">Click Here to Download File</a> <br /><br />
								<input type="file" name="GrantFile[ApplicationAssets]" id="UploadReceipts" class="string" onclick="r=confirm('File Already uploaded.Do you want to reupload?'); if (r==false) { return false; } else { return true; }" />
							{else}
								<input type="file" name="GrantFile[ApplicationAssets]" id="UploadReceipts" class="string" />
							{/if}
						</div>
					</div>
					<div class="formFieldContainer">
						<h4>{t}Showreel URL - please provide a link to examples of your previous work{/t} <span class="spanred"><b>*</b></span></h4>
						<p><input class="long required string" id="ShowReelURL" type="text" name="ShowReelURL" value="{if $oUser->getParamSet()->getParam('ShowReelURL')}{$oUser->getParamSet()->getParam('ShowReelURL')}{else}N/A{/if}" /></p>
					</div>
					<div>
						<input type="hidden" name="UserMovieGrantID" value="{$oGrant->getID()}" />
						<input type="hidden" name="GrantID" value="{$oGrant->getGrants()->getID()}" />
						{*<input id="userMovieGrantsSubmit" type="submit" name="submit" class="submit" value="Submit" onClick=" return confirm('Are you sure you wish to make this change to the grant?');"/>
						<input type="reset" name="reset" class="submit" value="Reset"/>
						<a href="/account/grants"><input type="button" name="cancel" class="submit" value="Cancel" /></a>*}
					</div>
					<div class="clearBoth"></div>
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
							<button type="submit" name="submit" value="Save" title="{t}Save{/t}" class="userMovieGrantsSubmitButton" id="userMovieGrantsSubmit">
								<img src="{$themeicons}/32x32/action-do-edit-object.png" alt="{t}Save{/t}" class="icon" />
								{t}Save Changes{/t}
							</button>
						</div>
						<div class="clearBoth"></div>
					</div>
				</form>
			
		</div>
		{else}
			<h2>
				<div>{t}We are sorry!  Grants application for the selected MOFILM Competition is not available.Please see our current open competitions <a href="{$mofilmWwwUri}/competitions/index.html">here</a>.{/t}</div>
			</h2>
		{/if}
		<br class="clearBoth">
	</div>
</div>
{include file=$oView->getTemplateFile('footer', 'shared')}