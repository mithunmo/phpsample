{include file=$oView->getTemplateFile('header', 'shared') pageTitle=''}
{include file=$oView->getTemplateFile('menu', 'shared')}
<div id="body">
<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>   
<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1">
    <div class="paymentwrapper">    
        <div class="paymenttopline">
            <h2>Request Payment</h2>
        </div>
        <div class="paymentform1" style="z-index: 0;">    
            <form name="addPaymentForm" id="addPaymentForm" method="post" action="/admin/paymentDetails/doAddPayment">
                <div class="paymenthead">
                    <h5>Project Details:</h5>
                </div>
 
                   
                <div class="row">
                     <!-- Event/Project -->
                    <div class="col-md-6">
                        <label class="paylabel">Event/Project Name:</label>
                        {eventSelect id="paymentEventList" name='EventID' selected=$oParam['EventID'] user=$oUser}
                        <div id="errorEvent" class="paymenterror"></div>
                    </div>
                    <!-- Brand -->
                    <div class="col-md-6">
                        <label class="paylabel">Brand:</label>
                       {brandSelect id="corporateListBrands" name='BrandID' selected=$oParam['BrandID'] EventID=$oParam['EventID'] }       
                        <div id="errorBrand" class="paymenterror"></div>
                    </div>
                </div>

                <!-- Filmmaker -->
                <div class="row" >
                    <div class="col-md-12">
                        <label class="paylabel">Filmmaker:</label>                     
                        <input id="contributors" class="contributorUser string ui-autocomplete-input form-control" type="text" placeholder="Filmmaker name" {if $oParam['FilmMaker'] != ''}value="{$oParam['FilmMaker']}"{else}value=""{/if} name="Contributors[{$index+1}][Name]" style="display: inline;" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true">                       
                        <input type="hidden" id="FilmMaker" name="FilmMaker" value="" />
                        <div id="errorFilmMaker" class="paymenterror"></div>
                    </div>                       
                </div>
                        
                <div class="row" style="margin-bottom: 20px;" >                   
                    <div class="col-md-6">
                        <label class="paylabel">Payment Type:</label>
                        <select id="PaymentType" name="PaymentType" class="form-control" >
                            <option value="">Select Type</option>
                            <option value="Edits">Edits</option>
                            <option value="Fee">Fee</option>
                            <option value="Production Fee">Production Fee</option>                          
                        </select>
                        <div id="errorPaymentType"  class="paymenterror"></div>
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
                            <input id="dollar" onkeydown="numericOnly(this,event)" name="TotalPayment" class="form-control" placeholder="" type="text">
                           
                        </div>
                         <div id="errorTotalAmount" class="paymenterror"></div>
                    </div>
                    <div class="col-md-1" style="margin:28px 0px 20px 0px;  max-width: 40px; "> 
                        <p class="paymenttxt">in</p>
                    </div>
                    <div class="col-md-4" style="margin:20px 0px 20px 0px; "> 
                        <select id="PaymentNumber" name="Paymentnumber" class="form-control" >
                            <option value="0"></option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                        <div id="errorPaymentNumber"  class="paymenterror"></div>
                    </div>
                    <div class="col-md-1" style="margin:28px 0px 20px 0px; "> 
                        <p class="paymenttxt">Payment(s)</p>
                    </div>
                </div>
                <div id="singlePayment" style="display:hidden;">
                </div>
                <div id="multiplePayment" style="display:hidden;">
                </div>    
                <div class="paymenttotal">
                    <div id="errorAmount" class="paymenterror"></div>
                    <h4 class="paymenttotaltitle">Total in Series:</h4>	
                    <h4 class="paytotalnumber">$ 0</h4>
                </div>
            </form>
        </div>
        <!-- Button -->
        <div class="paymenttopline">
            <div class="col-md-4 paymentbtn" style=" padding-right: 0; float: right; text-align: right;">
                <button id="submit" name="submit" class="btn-cancel" onclick="window.location='/admin/paymentDetails';">Cancel</button>
                <button id="button2id" name="buttonSubmit" class="btn-save">Save</button>
            </div>
        </div> 
    </div>
</div>
{include file=$oView->getTemplateFile('footer', 'shared')}