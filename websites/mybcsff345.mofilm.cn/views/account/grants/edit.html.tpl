{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Apply For Grants{/t} '}
{include file=$oView->getTemplateFile('menu', 'shared')}
<div id="body">
	<div class="container">
		{if $oGrant->getID() > 0 }
		<div>
				<h2>
					<div>{t}Apply Grants For{/t} - {$oGrant->getGrants()->getSource()->getEvent()->getName()} : {$oGrant->getGrants()->getSource()->getName()}</div>
				</h2>

				<div class="grantsLogoDisplay">
					<div style="display:inline"><strong>{t}Apply before{/t} </strong>2014年3月14日（5个名额）</div>
				</div>

				<form id="grantsApplyForm" class="userGrantsApplyForm" name="userGrantsForm" method="post" action="/account/grants/doEdit">
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
							<button type="submit" name="submit" value="Save" title="{t}Save{/t}" id="userMovieGrantsSubmit" onClick=" return confirm('{t}Are you sure you wish to make this change to the grant{/t}?');">
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
							{t}在进行您的制作资金支持申请前，我们强烈建议您仔细阅读成功申请制作资金支持指南{/t} : 
								<a href="http://www.mofilm.cn/mofilm-production-grants/" target="_blank">
									http://www.mofilm.cn/mofilm-production-grants/
								</a>
							<br /><br />
							注意事项<br />
							a. 创作资金非参赛的必要步骤，请根据实际情况申请；<br />
							b. 创作资金发放与最终评选结果无关；<br />
							c. 创作资金共设5个名额，请尽早申请，我们将在申请提交的一周后通过邮件方式与你取得联系，告知结果。如未获得资金支持，也希望大家积极拍摄影片，争取“优秀商业短片”大奖；<br />
							d. 创作资金为税前人民币数字，将在依法扣税后再发放给获奖者；<br />
							e. 每笔制作资金都将在影片提交后以费用报销的方式支付给申请人，所以请留存好花费凭证（发票、收据等）；<br />
							f. 如有疑问，请联系<a href="mailto:anita.huo@mofilm.com">anita.huo@mofilm.com。</a><br />
						</p>
					</div>
					
					<div class="content">
					<div class="formFieldContainer">
						<h4>{t}Title of your working film{/t} <span class="spanred"><b>*</b></span></h4>
						<p><input class="long required string" type="text" name="FilmTitle" value="{$oGrant->getFilmTitle()}" /></p>
					</div>
					<div class="formFieldContainer">
						<h4>{t}Please describe the concept of your film{/t} <span class="spanred"><b>*</b></span></h4>
						<p><textarea class="extralong required string" name="FilmConcept" cols="70" rows="10" />{$oGrant->getFilmConcept()}</textarea></p>
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
								<td colspan="2">{t}请填写人民币数额{/t}</td>
							    </tr>
							</table>
						</fieldset>
					</div>
					<div class="formFieldContainer">
						<h4>
							{t}近期拍摄作品链接{/t}
							{*<a href="mailto:productiongrant@mofilm.com">productiongrant@mofilm.com</a>*}
						</h4>
						<p><textarea class="extralong string" name="Script" cols="70" rows="10" />{$oGrant->getScript()}</textarea></p>
					</div>
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
							<button type="submit" name="submit" value="Save" title="{t}Save{/t}" id="userMovieGrantsSubmit" onClick=" return confirm('{t}Are you sure you wish to make this change to the grant?{/t}');">
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