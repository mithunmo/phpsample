{include file=$oView->getTemplateFile('header', 'shared') pageTitle=''}
{include file=$oView->getTemplateFile('menu', 'shared')}
 <div id="body" style="width: 100%; float: left;">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>   
    <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1">
    <div style="max-width:1100px; margin:0px auto; padding-top: 40px;">
        <div style="margin-bottom: 20px;">
        <a href="/admin/paymentDetails/" class="BacktoDB">Back to dashboard</a>
        <h2>Edit - Payment ID #{$oPaymentDetails['payment']['ID']}</h2>
        </div>
    </div>
    <form name="editPaymentForm" id="editPaymentForm" method="post" action="/admin/paymentDetails/doFinanceEdit">
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
                    <td><a href="/users/edit/{$oPaymentDetails['users']['ID']}">{$oPaymentDetails['users']['firstname']}{' '}{$oPaymentDetails['users']['surname']}</a>
                    </td>
                    <td>{$oPaymentDetails['payment']['paymentType']}</td>
                    <td>{$oPaymentDetails['payment']['status']}</td>
                    {if $oParams['param'] == 'changeAmount'}
                        <td>
                            <input type="text" name="payableAmount" id="payableAmount" value="{$oPaymentDetails['payment']['payableAmount']}" />
                            <div id="errorPayableAmount" class="paymenterror"></div>
                        </td>
                    {else}
                        <td>{$oPaymentDetails['payment']['payableAmount']}</td>
                    {/if}
                    <td>{$oPaymentDetails['payment']['created']}</td>
                    {if $oParams['param'] == 'changeDuedate'}
                        <td>
                            <input style="display: inline;"  id="tilldate" class="datepicker"  name="dueDate" type="text"  autofocuss value="{$oPaymentDetails['payment']['dueDate']}">
                            <div id="errorDueDate" class="paymenterror"></div>
                        </td>
                    {else}
                        <td>{$oPaymentDetails['payment']['dueDateDisplay']}</td>
                    {/if}
                    <td>-</td>
                </tr> 
            </tbody>                
        </table>
        <div class="generalband">
            <table>    
                <tr>
                    <td class="generalbandiv">
		    	<div class="glbandtxt"><img src="/themes/mofilm/images/payment/{if $oPaymentDetails['userMovieGrantsData']['DocumentBankDetails'] == 1}{'greenmark'}{else}{'redmark'}{/if}{'.png'}" alt="passmark" />Bank Details</div>
		    	<div class="glbandtxt"><img src="/themes/mofilm/images/payment/{if $oPaymentDetails['userMovieGrantsData']['DocumentIdProof'] == 1}{'greenmark'}{else}{'redmark'}{/if}{'.png'}" alt="passmark" />Photo ID Proof</div>
                    </td>
                    <td class="generalbandiv">
		    	<div class="glbandgrant"> Grant ID:
                            <div class="glbandlink"><a href="/grants/view/{$oPaymentDetails['payment']['grantID']}">{$oPaymentDetails['payment']['grantID']}</a></div>
		    	</div>
                    </td>
                    <td class="generalbandiv">
                        <div class="glbandtxt"><img src="/themes/mofilm/images/payment/{if $oPaymentDetails['userMovieGrantsData']['DocumentAgreement'] == 1}{'greenmark'}{else}{'redmark'}{/if}{'.png'}" alt="passmark" />Grant Approval</div>
		    	<div class="glbandtxt"><img src="/themes/mofilm/images/payment/{if $oPaymentDetails['userMovieGrantsData']['DocumentReceipts'] == 1}{'greenmark'}{else}{'redmark'}{/if}{'.png'}" alt="failmark" />Receipts</div>
                    </td>    	
                </tr>  
            </table>
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
        <div class="advancenote"> 
            <span> Add internal note:</span>  
             <textarea name="PaymentDesc" class="internalnote">{$oPaymentDetails['payment']['PaymentDesc']}</textarea>
            <div class="advantenotebtn">
                <button id="cancelEdit" name="cancelEdit" value="" class="btn btn-cancel" onclick="clearFinanceForm(); return false;"  >Cancel</button>
                <button id="financeEditSave" name="submit" value="" onclick="return validateFinanceForm();" class="btn btn-save">Save</button>
            </div>
        </div>
    </section>
    <input type="hidden" name="paymentID" value="{$oPaymentDetails['payment']['ID']}" />
    </form>            
</div>
{include file=$oView->getTemplateFile('footer', 'shared')}


