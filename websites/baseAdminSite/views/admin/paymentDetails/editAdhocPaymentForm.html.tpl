{include file=$oView->getTemplateFile('header', 'shared') pageTitle=''}
{include file=$oView->getTemplateFile('menu', 'shared')}
{assign var=multiPaymentCount value=count($oPaymentDetails['paymentList'])}
{assign var=i value=1}
<style>
.paylabel{ font-size: 12px;}
</style>
<div id="body">
<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>   
<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1">
    <div class="paymentwrapper">    
        <div class="paymenttopline">
            <h2>Edit Payment Series</h2>
        </div>
        <div class="paymentform1" style="z-index: 0;">    
            <form name="editPaymentForm" id="editPaymentForm" method="post" action="/admin/paymentDetails/doEditAdhoc">
                <div class="paymenthead">
                    <h5>Project Details:</h5>
                </div>
                  
                <div class="row">
                     <!-- Event/Project -->
                    <div class="col-md-6">
                        <label class="paylabel">Event/Project Name:</label>
                        {eventSelect id="paymentEventList" name='EventIDDisplay' selected=$oPaymentDetails['parentDetails'][0]['events']['ID']  disabled="disabled"}
                        <input type="hidden" id="EventID" name="EventID" value="{$oPaymentDetails['parentDetails'][0]['events']['ID']}" />
                    </div>
                    <!-- Brand -->
                    <div class="col-md-6">
                        <label class="paylabel">Brand:</label>      
                        {brandSelect id="corporateListBrands" name='BrandIDDisplay' selected=$oPaymentDetails['parentDetails'][0]['brands']['ID'] disabled="disabled" }       
                        <input type="hidden" id="BrandID" name="BrandID" value="{$oPaymentDetails['parentDetails'][0]['brands']['ID']}" />
                    </div>
                </div>

                <!-- Filmmaker -->
                <div class="row" >
                    <div class="col-md-12">
                        <label class="paylabel">Filmmaker:</label>
                       
                        <input id="contributors" class="contributorUser string ui-autocomplete-input form-control editDisable" type="text" placeholder="Filmmaker name" value="{$oPaymentDetails['parentDetails'][0]['users']['firstname']}{' '}{$oPaymentDetails['parentDetails'][0]['users']['surname']}" name="Contributors[{$index+1}][Name]" style="display: inline;" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true">
                        <input type="hidden" id="FilmMaker" name="FilmMaker" value="{$oPaymentDetails['parentDetails'][0]['users']['ID']}" />
                    </div>              
                </div>
                    
                <div class="row" style="margin-bottom: 20px;" >                   
                    <div class="col-md-6">
                        <label class="paylabel">Payment Type:</label>
                        <select id="PaymentTypeDisplay" name="PaymentTypeDisplay" class="form-control" disabled="disabled" >
                            <option {if $oPaymentDetails['parentDetails'][0]['payment']['paymentType'] == 'Edits'}{'selected'}{/if} value="Edits">Edits</option>
                            <option {if $oPaymentDetails['parentDetails'][0]['payment']['paymentType'] == 'Fee'}{'selected'}{/if} value="Edits">Fee</option>
                            <option {if $oPaymentDetails['parentDetails'][0]['payment']['paymentType'] == 'Production Fee'}{'selected'}{/if} value="Production Fee">Production Fee</option>                          
                        </select>
                        <input type="hidden" id="PaymentType" name="PaymentType" value="{$oPaymentDetails['parentDetails'][0]['payment']['paymentType']}" />
                    </div>                       
                </div>
                    
                <div class="paymenthead">
                    <h5>Payment Total:</h5>
                </div>

                <!-- Prepended total-->
                <div class="row">
                    <div class="col-md-5" style="margin:0px 0px 20px 0px;">
                        <label class="paylabel">Total:</label>
                        <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input id="editdollar" onkeydown="numericOnly(this,event)" name="TotalPayment" value="{$oPaymentDetails['parentDetails'][0]['payment']['payableAmount']}" class="form-control" placeholder="" type="text">
                           
                        </div>
                         <div id="errorTotalAmount" class="paymenterror"></div>
                    </div>
                    <div class="col-md-1" style="margin:28px 0px 20px 0px;  max-width: 40px; "> 
                        <p class="paymenttxt">in</p>
                    </div>
                    <div class="col-md-4" style="margin:20px 0px 20px 0px; "> 
                        <select id="PaymentNumberAdhoc" name="Paymentnumber" class="form-control" >
                            <option value="0"></option>
                            <option value="1" {if $multiPaymentCount == 1}{'selected'}{/if} {if $multiPaymentCount > 1}{'disabled'}{/if}>1</option>
                            <option value="2" {if $multiPaymentCount == 2}{'selected'}{/if} {if $multiPaymentCount > 2}{'disabled'}{/if}>2</option>
                            <option value="3" {if $multiPaymentCount == 3}{'selected'}{/if} {if $multiPaymentCount > 3}{'disabled'}{/if}>3</option>
                            <option value="4" {if $multiPaymentCount == 4}{'selected'}{/if} {if $multiPaymentCount > 4}{'disabled'}{/if}>4</option>
                        </select>
                        <div id="errorPaymentNumber"  class="paymenterror"></div>
                    </div>
                    <div class="col-md-1" style="margin:28px 0px 20px 0px; "> 
                        <p class="paymenttxt">Payment(s)</p>
                    </div>
                </div>

                    <div id="multiplePayment">
                        <div class="paymenthead"><h5>Payment Schedule:</h5></div>
                        {foreach $oPaymentDetails['paymentList'] as $payment}
                            {if $i > 1}
                                <div class="col-md-12"><hr class="paymenthr"/></div>
                            {/if}                                        
                            {assign var=fieldDisable value=''}
                            {assign var=showStatus value=0}
                            {if $payment['paymentDetails']['status'] == 'Canceled'}
                                {assign var=fieldDisable value='readonly'}
                            {/if}
                            {if $payment['paymentDetails']['status'] != 'Canceled'}
                                {assign var=paymentPercentage value=($payment['paymentDetails']['payableAmount']/$oPaymentDetails['parentDetails'][0]['payment']['payableAmount'])*100}
                            {/if}
                            <div class="row" style="padding:10px 20px;">
                                <div class="col-md-4">
                                    <label class="paylabel">P{$i}: #{$payment['paymentDetails']['ID']}, {$payment['paymentDetails']['status']}</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>
                                        <input id="partDollar_{$i}" onkeydown="numericOnly(this,event)" name="partPaymentAmount_{$i}" class="form-control partPayment" placeholder="" type="text" value="{$payment['paymentDetails']['payableAmount']}" {$fieldDisable}>
                                    </div>
                                     <div id="errorpartDollar_{$i}"  class="paymenterror"></div>
                                </div>
                                <div class="col-md-1" style="margin:28px 0px 20px 0px;max-width: 40px;"> 
                                    <p class="paymenttxt">/</p>
                                </div>
                                <div class="col-md-3" style="margin:20px 0px 20px 0px; ">
                                    <div class="input-group">
                                        <input id="percentage_{$i}" name="partPaymentPercentage_{$i}" class="form-control percentage" placeholder="" type="text" value="{$paymentPercentage}" {$fieldDisable}>
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </div>
                                {if $showStatus!= 1}
                                    <div class="col-md-4"  style="margin:26px 0px 20px 0px;">
                                        {if $payment['paymentDetails']['status'] != 'Canceled' }
                                            {if $oController->hasAuthority('paymentDetailsController.viewCompliance') || $oController->hasAuthority('paymentDetailsController.viewFinance') ||  $oUser->getPermissions()->isRoot() }
                                                <a class="cancelink"  onclick="return confirm('Are you sure to change the status?');" href="/admin/paymentDetails/doChangeStatusAdhoc/Canceled/{$oPaymentID}/{$payment['paymentDetails']['ID']}">Cancel</a>
                                            {/if}
                                        {/if} 
                                        {if $payment['paymentDetails']['status'] == 'Draft'}
                                            {if $oController->hasAuthority('paymentDetailsController.viewFinance') ||  $oUser->getPermissions()->isRoot() }
                                                <a  class="approvedlink" onclick="return confirm('Are you sure to change the status?');" href="/admin/paymentDetails/doChangeStatusAdhoc/ApprovedDraft/{$oPaymentID}/{$payment['paymentDetails']['ID']}">Approve Draft</a>
                                            {/if}
                                        {/if}
                                    </div>
                                {/if}
                            </div>
                            <div class="row" style="padding:10px 20px;">
                                <div class="col-md-4">
                                    <label class="paylabel">Time:</label>
                                    <select id="DateOn_{$i}" name="DateOn_{$i}" class="form-control">
                                        <option value="DateOn">On</option>
                                    </select>
                                </div>
                                <div class="col-md-4" style="margin:20px 0px 20px 0px; " >
                                    <select id="DateCondition_{$i}" name="DateCondition_{$i}" class="form-control">
                                        <option value="DateOf">Date Of</option>
                                    </select>
                                </div>
                                <div class="col-md-4" style="margin:15px 0px 20px 0px; ">
                                    <div class="input-group date" id="datetimepicker1" style="margin-top: 5px;">
                                        <span class="input-group-addon" >
                                            <span class="dataicon"><img src="/themes/mofilm/images/payment/dateicon.jpg" /></span>
                                        </span>
                                        <input style="width:115px;" id="datechoice_{$i}" class="datepicker form-control" type="text"   name="datechoice_{$i}" value="{substr($payment['paymentDetails']['dueDate'],0,10)}" {$fieldDisable} >
                                    </div>
                                    <div id="errordatechoice_{$i}" class="paymenterror"></div>
                                </div>
                            </div>
                            <input type="hidden"  name="paymentID_{$i}" value="{$payment['paymentDetails']['ID']}" />
                              <input type="hidden"  id="status_{$i}" name="status_{$i}" value="{$payment['paymentDetails']['status']}" />
                            {assign var=i value=$i+1}
                        {/foreach}
                        <div id="dynamicPaymentDiv"></div>
                         <input type="hidden"  id="totalParts" name="totalParts" value="{$multiPaymentCount}" />
                         <input type="hidden"  id="parentID" name="parentID" value="{$oPaymentDetails['parentDetails'][0]['payment']['ID']}" />
                    </div>   
                 
                <div class="paymenttotal">
                    <div id="errorAmount" class="paymenterror"></div>
                    <h4 class="paymenttotaltitle">Total in Series:</h4>	
                    <h4 class="paytotalnumber">$ {$oPaymentDetails['parentDetails'][0]['payment']['payableAmount']}</h4>
                </div>
            </form>
        </div>
        <!-- Button -->
        <div class="paymenttopline">
            <div class="col-md-4 paymentbtn" style=" padding-right: 0; float: right; text-align: right;">
                <a href="/admin/paymentDetails/edit/{$oPaymentID}" ><button id="submit" name="submit" class="btn btn-cancel"  >Cancel</button></a>
                <button id="button2id" name="buttonSubmit" class="btn-save">Save</button>
            </div>
        </div> 
    </div>
</div>
{if isset($oParams['budgetExceeded']) && $oParams['budgetExceeded'] == 1}            
    <div id="boxes"> 
        <div id="dialog" class="window warnningbox" style="top: 198px; left: 536.5px; display: block; ">
            <h2>Budget exceeded!</h2>
            <hr/>
            <p>The budget limit for this project would be exceeded by this payment. A revised budget must be agreed before proceeding.
            </p>
            <hr/>
            <div class="warnningbottom" style="height: 70px;">
                <a class="close warnningbtn" href="#">OK</a>
            </div>       
        </div>
        <div style="width: 1478px; font-size: 32pt; color:white; background: #222; height: 602px; display: none; opacity: 0.8;" id="mask"></div>
    </div>
{/if}
{include file=$oView->getTemplateFile('footer', 'shared')}