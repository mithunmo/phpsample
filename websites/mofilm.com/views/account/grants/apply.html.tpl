{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Apply For Grants{/t} '}
{include file=$oView->getTemplateFile('menu', 'shared')}
<div id="body">
	<div class="container">
		{if $oGrants->getID() > 0 && $message == 'Active' }
		<div>
				<h2>
					<div>{t}Apply Grants For{/t} - {$oSource->getEvent()->getName()} : {$oSource->getName()}</div>
				</h2>

				<div class="grantsLogoDisplay">
					<div style="display:inline"><strong>{t}Apply before{/t} </strong>{$oGrants->getEndDate()->getDate()|date_format:"%e %b , %Y"}</div>
				</div>
				
				{if $oGrants->isClosed()}
					<div class="spanred">
					    {t}The deadline for production grant applications for this contest has now passed. However, we sometimes have additional money become available if a granted filmmaker is unable to submit to the contest. So please complete the application form but understand that at this stage it might not be read by an account manager.{/t}
					</div>
				{/if}
			
				<form id="grantsApplyForm" class="userGrantsApplyForm" name="userGrantsForm" method="post" action="/account/grants/doApply" enctype="multipart/form-data">
				    	<div class="content">
						<div class="daoAction">
							<a href="/account/grants" title="{t}Cancel{/t}">
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
							{t}{$oGrants->getDescription()|nl2br}{/t}
						</p>
					</div>

					<div class="content">
					<div class="formFieldContainer">
						<h4>{t}Please describe the concept of your film{/t} <span class="spanred"><b>*</b></span></h4>
						<p><textarea class="extralong required string" name="FilmConcept" cols="70" rows="10" /></textarea></p>
					</div>
					<div class="formFieldContainer">
						<h4>{t}Title of your working film{/t} <span class="spanred"><b>*</b></span></h4>
						<p><input class="long required string" type="text" name="FilmTitle" value="" /></p>
					</div>
					<div class="formFieldContainer">
						<h4>{t}Proposed use of grant funding{/t} <span class="spanred"><b>*</b></span></h4>
						<p><textarea class="extralong required string" name="UsageOfGrants" cols="70" rows="10" /></textarea></p>
					</div>
					<div class="formFieldContainer">
						<fieldset style="width: 872px;">
							<legend>{t}Requested Amount{/t}</legend>
							<table width="100%" cellpadding="2" cellspacing="2">
							    <tr>
								<td>{t}Script writer{/t}</td>
								<td><b>{$oGrants->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="ScriptWriterAmount" id="ScriptWriterAmount" value="" /></td>
								<td>{t}Producer{/t}</td>
								<td><b>{$oGrants->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="ProducerAmount" id="ProducerAmount" value="" /></td>
								<td>{t}Director{/t}</td>
								<td><b>{$oGrants->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="DirectorAmount" id="DirectorAmount" value="" /></td>
							    </tr>
							    <tr>
								<td>{t}Talent{/t}</td>
								<td><b>{$oGrants->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="TalentAmount" id="TalentAmount" value="" /></td>
								<td>{t}DoP{/t}</td>
								<td><b>{$oGrants->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="DoPAmount" id="DoPAmount" value="" /></td>
								<td>{t}Editor{/t}</td>
								<td><b>{$oGrants->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="EditorAmount" id="EditorAmount" value="" /></td>
							    </tr>
							    <tr>
								<td>{t}Talent Expenses{/t}</td>
								<td><b>{$oGrants->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="TalentExpensesAmount" id="TalentExpensesAmount" value="" /></td>
								<td>{t}Production Staff{/t}</td>
								<td><b>{$oGrants->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="ProductionStaffAmount" id="ProductionStaffAmount" value="" /></td>
								<td>{t}Props{/t}</td>
								<td><b>{$oGrants->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="PropsAmount" id="PropsAmount" value="" /></td>
							    </tr>
							    <tr>
								<td>{t}Special Effects{/t}</td>
								<td><b>{$oGrants->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="SpecialEffectsAmount" id="SpecialEffectsAmount" value="" /></td>
								<td>{t}Wardrobe{/t}</td>
								<td><b>{$oGrants->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="WardrobeAmount" id="WardrobeAmount" value="" /></td>
								<td>{t}Hair and Make-up{/t}</td>
								<td><b>{$oGrants->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="HairMakeUpAmount" id="HairMakeUpAmount" value="" /></td>
							    </tr>
							    <tr>
								<td>{t}Camera rental{/t}</td>
								<td><b>{$oGrants->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="CameraRentalAmount" id="CameraRentalAmount" value="" /></td>
								<td>{t}Sound{/t}</td>
								<td><b>{$oGrants->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="SoundAmount" id="SoundAmount" value="" /></td>
								<td>{t}Lighting{/t}</td>
								<td><b>{$oGrants->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="LightingAmount" id="LightingAmount" value="" /></td>
							    </tr>
							    <tr>
								<td>{t}Transportation{/t}</td>
								<td><b>{$oGrants->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="TransportationAmount" id="TransportationAmount" value="" /></td>
								<td>{t}Crew Expenses{/t}</td>
								<td><b>{$oGrants->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="CrewExpensesAmount" id="CrewExpensesAmount" value="" /></td>
								<td>{t}Location{/t}</td>
								<td><b>{$oGrants->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="LocationAmount" id="LocationAmount" value="" /></td>
							    </tr>
							    <tr>
								<td colspan="4"></td>
								<td>{t}Others{/t}</td>
								<td><b>{$oGrants->getCurrencySymbol()} </b> <input class="small calculateAmount" type="text" name="OthersAmount" id="OthersAmount" value="" /></td>
							    </tr>
							    <tr>
								<td colspan="5" align="right"><h3><strong>{t}Total{/t}</strong></h3></td>
								<td><h3><strong>{$oGrants->getCurrencySymbol()} <span id="TotalGrantAmount"></span></strong></h3></td>
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
						<p><textarea class="extralong string" name="Script" cols="70" rows="10" /></textarea></p>
					</div>
							
					<div class="formFieldContainer">
						<h4>{t}Supporting Documents{/t}</h4>
						<input type="file" name="GrantFile[ApplicationAssets]" id="UploadReceipts" class="string" />
					</div>

					<div class="formFieldContainer">
						<h4>{t}Showreel URL - please provide a link to examples of your previous work{/t} <span class="spanred"><b>*</b></span></h4>
						<p><input class="long required string" id="ShowReelURL" type="text" name="ShowReelURL" value="{if $oUser->getParamSet()->getParam('ShowReelURL')}{$oUser->getParamSet()->getParam('ShowReelURL')}{/if}" /></p>
					</div>
						
					</div>
					<div>
						<input type="hidden" name="GrantID" value="{$oGrants->getID()}" />
					</div>
					<div class="clearBoth"></div>
					<div class="content">
						<div class="daoAction">
							<a href="/account/grants" title="{t}Cancel{/t}">
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
		{elseif $message == 'Expired'}
			<h2>
				<div>{t}We are sorry!  Grants application for the selected MOFILM Competition is closed. Please see our current open competitions{/t} <a href="{$mofilmWwwUri}/competitions/index.html">{t}here{/t}</a>.</div>
			</h2>
		{elseif $message == 'Applied'}
			<h2>
				<div>{t}You have already applied for grants for the selected MOFILM Competition{/t}. <br /> {t}If you have more ideas or concept to submit, you can do this by clicking{/t} <a href="/account/grants/edit/{$inAppliedGrantID}">{t}here{/t}</a>. <br />{t}Please see our current open competitions{/t} <a href="{$mofilmWwwUri}/competitions/index.html">{t}here{/t}</a>.</div>
			</h2>
		{else}
			<h2>
				<div>{t}We are sorry!  Grants application for the selected MOFILM Competition is not available.Please see our current open competitions{/t} <a href="{$mofilmWwwUri}/competitions/index.html">{t}here{/t}</a>.</div>
			</h2>
		{/if}
		<br class="clearBoth">
	</div>
</div>
{include file=$oView->getTemplateFile('footer', 'shared')}