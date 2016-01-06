<form class="paymentform" action="/admin/paymentDetails/{$oController->getAction()}" method="get" name="paymentSearchForm" id="paymentSearchForm" accept-charset="utf-8">
                    {eventSelect id="paymentEventList" name='EventID' selected=$oParam['EventID'] user=$oUser}
                    {brandSelect id="corporateListBrands" name='BrandID' selected=$oParam['BrandID'] EventID=$oParam['EventID'] }       
                    <div class="contributors" style="display: inline;float:left;">
                        <input type="text"  placeholder="Filmmaker Name" style="display: inline;width:210px;" id="contributors" class="contributorUser string" name="Contributors[{$index}][Name]" {if $oParam['FilmMaker'] != ''}value="{$oParam['FilmMaker']}"{else}value=""{/if}  />
                        <input type="hidden" id="FilmMaker" name="FilmMaker" {if $oParam['FilmMakerID'] != ''}value="{$oParam['FilmMakerID']}"{else}value=""{/if} />
                    </div>
                    <select id="paymentType" name="PaymentType" class="form-control">
                        <option value="">Type</option>
                        <option value="Production Fee" {if $oParam['PaymentType'] == 'Production Fee'}{'selected'}{/if}>Production Fee</option>
                        <option value="Fee" {if $oParam['PaymentType'] == 'Fee'}{'selected'}{/if}>Fee</option>
                        <option value="Edits" {if $oParam['PaymentType'] == 'Edits'}{'selected'}{/if}>Edits</option>
                        <option value="Advance Grant" {if $oParam['PaymentType'] == 'Advance Grant'}{'selected'}{/if}>Advance Grant</option>
                        <option value="Prize" {if $oParam['PaymentType'] == 'Prize' }{'selected'}{/if}>Prize</option>
                        <option value="Grant" {if $oParam['PaymentType'] == 'Grant' }{'selected'}{/if}>Grant</option>
                    </select>
                    {if $oController->getAction() != 'duePayments' &&  $oController->getAction() != 'viewFinance' && $oController->getAction() != 'newPayments'}
                    <select id="status" name="Status" class="form-control">
                        {if (!$oUser->getPermissions()->isRoot() && $oController->hasAuthority('paymentDetailsController.viewCompliance')) || $oController->getAction() == 'viewCompliance'}
                       
                        {else}
                             <option value="">Status</option>
                        {/if}
                        <option value="Pending Approval" {if $oParam['Status'] == 'Pending Approval'}{'selected'}{/if}>Pending Approval</option>
                        <option value="Approved" {if $oParam['Status'] == 'Approved'}{'selected'}{/if}>Approved</option>
                        <option value="Canceled" {if $oParam['Status'] == 'Canceled'}{'selected'}{/if}>Canceled</option>
                        <option value="Draft" {if $oParam['Status'] == 'Draft'}{'selected'}{/if}>Draft</option>
                        <option value="Paid" {if $oParam['Status'] == 'Paid'}{'selected'}{/if}>Paid</option>
                    </select>
                    {/if}
                    <select style="width:90px;" id="status" name="DateFilter" class="form-control">
                        <option value="created" {if $oParam['DateFilter'] == 'created'}{'selected'}{/if}>Created on</option>
                        <option value="dueDate" {if $oParam['DateFilter'] == 'dueDate'}{'selected'}{/if}>Due on</option>
                        <option value="paidDate" {if $oParam['DateFilter'] == 'paidDate'}{'selected'}{/if}>Paid on</option> 
                    </select>
                    <input style="display: inline;"  id="createdon" class="datepicker"  name="FromDate" type="text"  autofocuss {if $oParam['FromDate'] != ''}value='{$oParam['FromDate']}'{else} value=''{/if}>
                    <span style="padding:0px 8px; color:#737677; font-size: 13px;"> To </span>
                    <input style="display: inline;"  id="tilldate" class="datepicker"  name="ToDate" type="text"  autofocuss {if $oParam['ToDate'] != ''}value='{$oParam['ToDate']}'{else} value=''{/if}>
                    <button id="searchButtonSubmit" class="paysearch" > <a  id="searchButton">&nbsp;</a> </button>
                    {if $oUser->getPermissions()->isRoot() || $oController->hasAuthority('paymentDetailsController.viewCompliance') || $oController->hasAuthority('paymentDetailsController.viewAccountManger')}
                        {if $oController->getAction() != 'duePayments' &&  $oController->getAction() != 'viewFinance'}
                            <a class="newpay" href="/admin/paymentDetails/addPayment"> Request Payment</a>
                        {/if}
                    {/if}
                   
                    
                </form>