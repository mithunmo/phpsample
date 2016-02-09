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
                    <td>{$oPaymentDetails['payment']['payableAmountDisplay']}</td>
                    <td>{$oPaymentDetails['payment']['created']}</td>
                    <td>{$oPaymentDetails['payment']['dueDate']}</td>
                    <td>-</td>
                </tr> 
            </tbody>                  
        </table>
        <form name="addAdvanceForm" id="addAdvanceForm" method="post" action="/admin/paymentDetails/doAdvanceGrant">
            <div class="advancebottom">
                <div class="advancecolumns">
                    <span>On date:</span>
                    <div class="advanceinput1" id="datetimepicker1" style="margin-top: 5px;">
                        <input style="display: inline;"  id="advanceDate" class="datepicker"  name="dueDate" type="text"  autocomplete="off">
                        <div id="errorAdvanceDate" class="paymenterror"></div>
                    </div>
                </div>
                <div class="advancecolumns">
                    <span>Amount requested:</span>
                    <div class="advanceinput" id="datetimepicker1" style="margin-top: 5px;">
                        <input style="display: inline;"  onkeydown="numericOnly(this,event)" id="advanceAmount"   name="payableAmount" type="text"  autocomplete="off">
                        <input type="hidden" name="grantAmount" id="grantAmount" value="{$oPaymentDetails['payment']['payableAmount']}" />
                        <input type="hidden" name="grantDueDate" id="grantDueDate" value="{$oPaymentDetails['payment']['dueDate']}" />
                        <input type="hidden" name="paymentID" id="paymentID" value="{$oPaymentDetails['payment']['ID']}" />
                        <div id="errorAdvanceAmount" class="paymenterror"></div>
                    </div>
                </div>
            </div>
        
        <div class="advancenote"> 
            <span> Add internal note:</span>  
            <textarea name="PaymentDesc" class="internalnote">{$oPaymentDetails['payment']['PaymentDesc']}</textarea>   
            <div class="advantenotebtn">
                <button id="cancelSubmit" name="cancelSubmit" class="btn btn-cancel" onclick="clearAdvanceForm(); return false;" >Cancel</button>
                <button id="advanceSave"  name="submit" class="btn btn-save">Save</button>
            </div>
        </div>
        </form>
       
    </section>
</div>
{include file=$oView->getTemplateFile('footer', 'shared')}