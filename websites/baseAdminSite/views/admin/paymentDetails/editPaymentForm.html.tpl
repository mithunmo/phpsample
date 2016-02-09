{include file=$oView->getTemplateFile('header', 'shared') pageTitle=''}
{include file=$oView->getTemplateFile('menu', 'shared')}
<div id="body" style="width: 100%; float: left;">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>   
    <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1">
    <div style="max-width:1100px; margin:0px auto; padding-top: 40px;">
        <div style="margin-bottom: 20px;">
        {if $oController->getPageType() != ''}
            <a href="/admin/paymentDetails/{$oController->getPageType()}?{$oController->getCallBackQuery()}" class="BacktoDB">Back to dashboard</a>
        {else}
            <a href="/admin/paymentDetails/?{$oController->getCallBackQuery()}" class="BacktoDB">Back to dashboard</a>
        {/if}
        <h2>Edit - Payment ID #{$oPaymentDetails['payment']['ID']}</h2>
        </div>
    </div> 
    <section class="tabsection" style="max-width: 1100px;">
        <table class="payformtable" style="width:100%; overflow:auto;">
            <thead>
                <tr>
                    <th style="width:12%;">Project</th>
                    <th style="width:12%;">Brand</th>
                    <th style="width:12%;">Filmmaker</th>
                    <th style="width:8%;">Type</th>
                    <th style="width:8%;">Status</th>
                    <th style="width:10%;">Amount</th>
                    <th style="width:12%;">Created on</th>
                    <th style="width:12%;">Due on</th>
                    <th style="width:12%;">Paid on</th>
                 </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <h5>{$oPaymentDetails['events']['name']}</h5>
                        <h6>{$oPaymentDetails['products']['name']}</h6>
                    </td>
                    <td>{$oPaymentDetails['brands']['name']}</td>
                    <td><a href="{system::getConfig()->getParam('mofilm', 'platformUri')->getParamValue()}{'users/'}{$oPaymentDetails['users']['ID']}{'?token='}{$accessToken}">{$oPaymentDetails['users']['firstname']}{' '}{$oPaymentDetails['users']['surname']}</a>
                    </td>
                    <td>{$oPaymentDetails['payment']['paymentType']}</td>
                    <td>{$oPaymentDetails['payment']['status']}</td>
                    <td>$ {$oPaymentDetails['payment']['payableAmountDisplay']}</td>
                    <td>{$oPaymentDetails['payment']['created']}</td>
                    <td>{$oPaymentDetails['payment']['dueDate']}</td>
                    <td>{$oPaymentDetails['payment']['paidDate']}</td>
                </tr> 
            </tbody>                
        </table>
        {if $oPaymentDetails['payment']['paymentType'] != 'Fee'  && $oPaymentDetails['payment']['paymentType'] != 'Production Fee' && $oPaymentDetails['payment']['paymentType'] != 'Edits'}
            <div class="generalband">
                <table>    
                    <tr>
                        <td class="generalbandiv">
                            <div class="glbandtxt"><img src="/themes/mofilm/images/payment/{$oPaymentDetails['userMovieGrantsData']['DocumentBankDetails']}{'.png'}" alt="{$oPaymentDetails['userMovieGrantsData']['DocumentBankDetailsMessage']}" />Bank Details</div>
                            <div class="glbandtxt"><img src="/themes/mofilm/images/payment/{$oPaymentDetails['userMovieGrantsData']['DocumentIdProof']}{'.png'}" alt="{$oPaymentDetails['userMovieGrantsData']['DocumentIdProofMessage']}" />Photo ID Proof</div>
                        </td>
                        
                        {if $oPaymentDetails['payment']['paymentType'] != 'Prize'}
                            <td class="generalbandiv">
                                <div class="glbandgrant"> Grant ID:
                                    <div class="glbandlink"><a href="/grants/view/{$oPaymentDetails['payment']['grantID']}">{$oPaymentDetails['payment']['grantID']}</a></div>
                                </div>
                            </td>
                            <td class="generalbandiv">
                                <div class="glbandtxt"><img src="/themes/mofilm/images/payment/{$oPaymentDetails['userMovieGrantsData']['DocumentAgreement']}{'.png'}" alt="{$oPaymentDetails['userMovieGrantsData']['DocumentAgreementMessage']}" />Grant Approval</div>
                                <div class="glbandtxt"><img src="/themes/mofilm/images/payment/{$oPaymentDetails['userMovieGrantsData']['DocumentReceipts']}{'.png'}" alt="{$oPaymentDetails['userMovieGrantsData']['DocumentReceiptsMessage']}" />Receipts</div>
                            </td> 
                        {else}
                            <td class="generalbandiv">
                                <div class="glbandgrant"> Movie ID:
                                    <div class="glbandlink"><a href="/videos/edit/{$oPaymentDetails['payment']['movieID']}">{$oPaymentDetails['payment']['movieID']}</a></div>
                                </div>
                            </td>
                            <td class="generalbandiv">
                                <div class="glbandtxt"><img src="/themes/mofilm/images/payment/{$oPaymentDetails['MovieAssets']['FilmmakerAgreement']}{'.png'}" alt="{$oPaymentDetails['MovieAssets']['FilmmakerAgreementMessage']}" />Filmmaker Agreement</div>
                                <div class="glbandtxt"></div>
                            </td> 
                        {/if}                     
                          	
                    </tr>  
                </table>
            </div>
        {/if}           
        <div class="generalcolorbtn">
            {if $oPaymentDetails['payment']['paymentType'] != 'Fee' && $oPaymentDetails['payment']['paymentType'] != 'Production Fee' && $oPaymentDetails['payment']['paymentType'] != 'Edits'}
                {if $oUser->getPermissions()->isRoot()}  
                    {if $oPaymentDetails['payment']['status'] == 'Canceled' }
                        <a onclick="return confirm('Are you sure to change the status?');" ref="/admin/paymentDetails/doChangeStatus/Approved/{$oPaymentDetails['payment']['ID']}?{$oController->getCallBackQuery()}" ><button id="approve" name="submit" class="btn btn-approved">Approve Payment</button></a>
                    {elseif $oPaymentDetails['payment']['status'] == 'Pending Approval' }
                        <a onclick="return confirm('Are you sure to change the status?');" href="/admin/paymentDetails/doChangeStatus/Approved/{$oPaymentDetails['payment']['ID']}?{$oController->getCallBackQuery()}" ><button id="approve" name="submit" class="btn btn-approved">Approve Payment</button></a>
                    {/if} 
                    {if $oPaymentDetails['payment']['paymentType'] == 'Grant' && (strtotime("-9 days",strtotime($oPaymentDetails['payment']['dueDate'])) > strtotime('today')) }
                        <a href="/admin/paymentDetails/addAdvanceGrant/{$oPaymentDetails['payment']['ID']}" ><button id="submit" name="submit" class="btn btn-requestadv">Request Advance</button><a>
                    {/if}
                    {if $oPaymentDetails['payment']['status'] != 'Paid' }
                        <a href="/admin/paymentDetails/financeEdit/changeAmount/{$oPaymentDetails['payment']['ID']}" ><button id="approve" name="submit" class="btn btn-requestadv">Change Amount</button></a>
                        <a href="/admin/paymentDetails/financeEdit/changeDuedate/{$oPaymentDetails['payment']['ID']}" ><button id="approve" name="submit" class="btn btn-modify">Change Due Date</button></a> 
                    {/if} 

                    {if $oPaymentDetails['payment']['status'] != 'Canceled' && $oPaymentDetails['payment']['status'] != 'Paid'}
                        <a onclick="return confirm('Are you sure to change the status?');" href="/admin/paymentDetails/doChangeStatus/Canceled/{$oPaymentDetails['payment']['ID']}?{$oController->getCallBackQuery()}" ><button id="button2id" name="button2id" class="btn btn-cancelpayment">Cancel Payment</button></a>
                    {/if}  
                {elseif $oController->hasAuthority('paymentDetailsController.viewAccountManger')}
                    {if $oPaymentDetails['payment']['paymentType'] == 'Grant' && $oPaymentDetails['payment']['status'] != 'Paid' && (strtotime("-9 days",strtotime($oPaymentDetails['payment']['dueDate'])) > strtotime('today')) }
                        <a href="/admin/paymentDetails/addAdvanceGrant/{$oPaymentDetails['payment']['ID']}" ><button id="submit" name="submit" class="btn btn-requestadv">Request Advance</button></a>
                    {/if}
                {elseif $oController->hasAuthority('paymentDetailsController.viewCompliance')} 
                    {if $oPaymentDetails['payment']['status'] == 'Canceled'}
                        <a onclick="return confirm('Are you sure to change the status?');" href="/admin/paymentDetails/doChangeStatus/Approved/{$oPaymentDetails['payment']['ID']}?{$oController->getCallBackQuery()}" ><button id="approve" name="submit" class="btn btn-approved">Approve Payment</button></a>
                    {elseif $oPaymentDetails['payment']['status'] == 'Pending Approval' }
                        <a onclick="return confirm('Are you sure to change the status?');" href="/admin/paymentDetails/doChangeStatus/Approved/{$oPaymentDetails['payment']['ID']}?{$oController->getCallBackQuery()}" ><button id="approve" name="submit" class="btn btn-approved">Approve Payment</button></a>
                    {/if} 
                    {if $oPaymentDetails['payment']['paymentType'] == 'Grant' && (strtotime("-9 days",strtotime($oPaymentDetails['payment']['dueDate'])) > strtotime('today')) }
                        <a href="/admin/paymentDetails/addAdvanceGrant/{$oPaymentDetails['payment']['ID']}" ><button id="submit" name="submit" class="btn btn-requestadv">Request Advance</button><a>
                    {/if}
                    {if $oPaymentDetails['payment']['status'] != 'Canceled' && $oPaymentDetails['payment']['status'] != 'Paid'}
                        <a onclick="return confirm('Are you sure to change the status?');" href="/admin/paymentDetails/doChangeStatus/Canceled/{$oPaymentDetails['payment']['ID']}?{$oController->getCallBackQuery()}" ><button id="button2id" name="button2id" class="btn btn-cancelpayment">Cancel Payment</button></a>
                    {/if} 
                {elseif $oController->hasAuthority('paymentDetailsController.viewFinance')} 
                    {if $oPaymentDetails['payment']['status'] != 'Paid' }
                        <a href="/admin/paymentDetails/financeEdit/changeAmount/{$oPaymentDetails['payment']['ID']}" ><button id="approve" name="submit" class="btn btn-requestadv">Change Amount</button></a>
                        <a href="/admin/paymentDetails/financeEdit/changeDuedate/{$oPaymentDetails['payment']['ID']}" ><button id="approve" name="submit" class="btn btn-modify">Change Due Date</button></a>
                    {/if}            
                    {if $oPaymentDetails['payment']['status'] != 'Canceled' && $oPaymentDetails['payment']['status'] != 'Paid'}
                        <a onclick="return confirm('Are you sure to change the status?');" href="/admin/paymentDetails/doChangeStatus/Canceled/{$oPaymentDetails['payment']['ID']}?{$oController->getCallBackQuery()}" ><button id="button2id" name="button2id" class="btn btn-cancelpayment">Cancel Payment</button></a>
                    {/if}
                {/if}
            {else}
                <div class="invoicecontentop">
                    {if $oPaymentDetails['payment']['InvoiceCount'] == 0}
                        <h3 class="noinvoicewarning">No invoice submitted</h3>
                    {else}
                        <h3 class="noinvoicewarning">Invoice submitted</h3>
                    {/if}
                    <button class="invoiceuploadbtn" id="fakeBrowse" onclick="HandleBrowseClick();"> Upload Invoice </button>
                    <p id="showFilename" style="float: left;display: inline;"></p>
                    {if $oPaymentDetails['payment']['status'] != 'Paid' }
                        <a  href="/admin/paymentDetails/editAdhoc/{$oPaymentDetails['payment']['ID']}" ><button id="button2id" name="button2id" class="btn btn-modify">Modify Payment Series</button></a>
                    {/if}
                    {if $oPaymentDetails['payment']['status'] == 'Pending Approval' }
                        <a onclick="return confirm('Are you sure to change the status?');" href="/admin/paymentDetails/doChangeStatus/Approved/{$oPaymentDetails['payment']['ID']}?{$oController->getCallBackQuery()}" ><button id="approve" name="submit" class="btn btn-approved">Approve Payment</button></a>
                    {/if}
                </div>
            {/if}
        </div>    
        <div class="activitylog">
            <h3>Activity Log</h3>
            <table>
                {assign var=i value=1}
                {foreach $oPaymentDetails['PaymentLog'] as $paymentLog}
                <tr>
                    <td style="width:5%;">{$i}.</td>
                    <td style="width:15%;">{$paymentLog['logTime']}</td>
                    <td>{$paymentLog['comments']}</td>
                </tr> 
                {assign var=i value=$i+1}
                {/foreach}
            </table> 
        </div> 
        <form name="editForm" id="editForm" enctype="multipart/form-data"  method="post" action="/admin/paymentDetails/doSaveEdit/{$oPaymentDetails['payment']['ID']}" />
        <div class="advancenote"> 
            <span> Add internal note:</span>  
            <textarea name="PaymentDesc" class="internalnote">{$oPaymentDetails['payment']['PaymentDesc']}</textarea>
            <input type="file" id="browse" name="fileupload" style=" opacity:0;width:0px;height:0px;" />
            <input type="hidden" id="filename" name="filename"/>    
            <div class="advantenotebtn">
                <button id="submit" name="submit" class="btn btn-cancel paymentEditCancel"  >Cancel</button>
                <button id="saveEditButton" name="button2id" class="btn btn-save">Save</button>
                {if $oPaymentDetails['payment']['status'] == 'Draft' && $oPaymentDetails['payment']['status'] != 'Canceled'}
                    {if $oController->hasAuthority('paymentDetailsController.viewFinance') ||  $oUser->getPermissions()->isRoot() }
                        <a  href="/admin/paymentDetails/doChangeStatus/ApprovedDraft/{$oPaymentDetails['payment']['ID']}?{$oController->getCallBackQuery()}"><button id="button2idDraft" name="ApproveDraftButton" class="btn btn-approvedraft" >Approve Draft</button></a>
                    {/if}
                {/if}
            </div>          
        </div>
        </form>  
    </section>
</div>
{if isset($oParams['budgetExceeded']) && $oParams['budgetExceeded'] == 1}            
    <div id="boxes"> 
        <div id="dialog" class="window warnningbox" style="top: 198px; left: 536.5px; display: block; ">
            <h2>Budget exceeded!</h2>
            <hr/>
            <p>The budget limit for this project would be exceeded by this payment. A revised budget must be agreed before proceeding.
            </p>
            <hr/>
            <div class="warnningbottom" style="height: 35px;">
                <a class="close warnningbtn" href="#">OK</a>
            </div>       
        </div>
        <div style="width: 1478px; font-size: 32pt; color:white; background: #222; height: 602px; display: none; opacity: 0.8;" id="mask"></div>
    </div>
{/if}
{include file=$oView->getTemplateFile('footer', 'shared')}


