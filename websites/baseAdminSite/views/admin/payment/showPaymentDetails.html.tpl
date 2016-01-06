{include file=$oView->getTemplateFile('header', 'shared') pageTitle=$oMap->getDescription()}
{include file=$oView->getTemplateFile('menu', 'shared')}
	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
                        
                        

			<div class="floatLeft sideBar">
				{if !isset($parentController)}{assign var=parentController value='admin'}{/if}
				{generatePath controller=$oMap->getUriPath() parent=$parentController}
			</div>

			<div class="floatLeft main">
                            <form id="adminFormData" name="formData" method="post" action="/admin/payment/" accept-charset="utf-8">
                                    <h2>{t}Payment Details{/t}</h2>
                                     <div class="daoAction">
                                        <button type="submit" name="View List" title="{t}View List{/t}">
                                                <img src="{$themeicons}/32x32/other.png" alt="{t}View List{/t}" class="icon" />
                                                {t}View List{/t}
                                        </button>
                                     </div>
                            </form>
                            <div class="content">
                                <div class="filters">
                                    <div style="display:inline;float:left;padding-right:10px;width:135px;" >User :</div>
                                    <div ><a href="/users/edit/{$oObject->getUserID()}">{$oModel->getUserName($oObject->getUserID())}</a></div>
                                </div>  
                                <div class="filters">
                                    <div style="display:inline;float:left;padding-right:10px;width:135px;">Event :</div>
                                    <div >{$transactionDetails['EventName']}</div>
                                </div>
                                <div class="filters">
                                    <div style="display:inline;float:left;padding-right:10px;width:135px;">Source :</div>
                                    <div >{$transactionDetails['SourceName']}</div>
                                </div>
                                <div class="filters">
                                    <div style="display:inline;float:left;padding-right:10px;width:135px;">Grant :</div>
                                    <div >{if $oObject->getGrantID() != '0'}{t}<a href="/grants/view/{$oObject->getGrantID()}">{$oObject->getGrantID()}</a>{/t}{else}{'-'}{/if}</div>
                                </div>  
                                <div class="filters">
                                    <div style="display:inline;float:left;padding-right:10px;width:135px;">Movie :</div>
                                    <div ><a href="/videos/edit/{$transactionDetails['MovieID']}">{$transactionDetails['MovieName']}</a></div>
                                </div>
                                <div class="filters">
                                    <div style="display:inline;float:left;padding-right:10px;width:135px;">Payment Type :</div>
                                    <div >{$oObject->getPaymentType()}</div>
                                </div>
                                <div class="filters">
                                    <div style="display:inline;float:left;padding-right:10px;width:135px;">Comments :</div>
                                    <div >{if $oObject->getComments() == ''}{'-'}{else}{$oObject->getComments()}{/if}</div>
                                </div>
                                <div class="filters">
                                    <div style="display:inline;float:left;padding-right:10px;width:135px;">Submitter :</div>
                                    <div ><a href="/users/edit/{$oObject->getSubmitterID()}">{$oModel->getUserName($oObject->getSubmitterID())}</a></div>
                                </div>  
                                <div class="filters">
                                    <div style="display:inline;float:left;padding-right:10px;width:135px;">Approver :</div>
                                    <div ><a href="/users/edit/{$oObject->getApproverID()}">{$oModel->getUserName($oObject->getApproverID())}</a></div>
                                </div>
                                <div class="filters">
                                    <div style="display:inline;float:left;padding-right:10px;width:135px;">Approver Comments :</div>
                                    <div >{if $oObject->getApproverComments() == ''}{'-'}{else}{$oObject->getApproverComments()}{/if}</div>
                                </div>
                                <div class="filters">
                                    <div style="display:inline;float:left;padding-right:10px;width:135px;">Amount($) :</div>
                                    <div >{$oObject->getAmountGrant()}</div>
                                </div>  
                                <div class="filters">
                                    <div style="display:inline;float:left;padding-right:10px;width:135px;">Status :</div>
                                    <div >{$oObject->getStatus()}</div>
                                </div>
                                <div class="filters">
                                    <div style="display:inline;float:left;padding-right:10px;width:135px;">Created On :</div>
                                    <div >{$transactionDetails['created']}</div>
                                </div>      
                                <div class="filters">
                                    <div style="display:inline;float:left;padding-right:10px;width:135px;">Payment Date :</div>
                                    <div >{$transactionDetails['PaymentDate']}</div>
                                </div>  
                                <div class="filters">
                                    <div style="display:inline;float:left;padding-right:10px;width:135px;">Paid By:</div>
                                    <div >{if $oObject->getAccountUser() != 0}{t}<a href="/users/edit/{$oObject->getAccountUser()}">{/t}{$oModel->getUserName($oObject->getAccountUser())}</a>{else}{'-'}{/if}</div>
                                </div>
                                <div class="filters">
                                    <div style="display:inline;float:left;padding-right:10px;width:135px;">Payment Comments :</div>
                                    <div >{if $oObject->getPaymentDesc() == ''}{'-'}{else}{$oObject->getPaymentDesc()}{/if}</div>
                                </div>
                              
                            </div>
                                <div class="clearBoth"></div>
                            </div>
					
				
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}