{if $oController->getAction() == 'duePayments' ||  $oController->getAction() == 'viewFinance'}
    <div class="tabsmenu">
        <label id="subtabactive"><a href="/admin/paymentDetails/duePayments" checked >Payment Due</a></label>
        <label id="tab4"><a href="/admin/paymentDetails/newPayments" >New payments</a></label>
    </div>
{elseif $oController->getAction() == 'newPayments'}
    <div class="tabsmenu">
        <label id="tab5"><a href="/admin/paymentDetails/duePayments" >Payment Due</a></label>
        <label id="subtabactive"><a href="/admin/paymentDetails/newPayments" checked>New payments</a></label>
    </div>
{/if}
<div id="content1">     
    <div class="accordion vertical">
    {if $oController->getAction() == 'duePayments' ||  $oController->getAction() == 'viewFinance'}
        <div class="paymentcontent"> 
            <h3 class="synctitle"> Payments Sync.</h3> 
            <div class="syncwrap"> 
                <div class="synccontent">
                    <p>
                        {if $csvUpdatedMsg != ''}{$csvUpdatedMsg}<br/>{/if}
                    Download CSV for payments shown, update with a '1' where paid, and import to update record. You will have the opportunity to review / edit before saving any changes. </p> 
                    <div class="syncright">
                        <a href="/admin/paymentDetails/export/?{$rawDaoSearchQuery}&PageNo={$oFinanceList['paymentPagination']['PageNo']}"><button id="download" name="submit" class="btn btn-download">Download</button></a>
                        <a href="/admin/paymentDetails/import"><button id="import" name="submit" class="btn btn-import">Import</button></a>
                        <!--<span class="syncdivide">|</span> 
                        <button id="cancel" name="submit" class="btn btn-cancel">Cancel</button> 
                        <button id="save" name="submit" class="btn btn-save">Save</button> -->
                    </div>             
                </div> 
                {include file=$oView->getTemplateFile('searchForm')}
            </div>
        {include file=$oView->getTemplateFile('financeDetailsList')}
        </div>
    {elseif $oController->getAction() == 'newPayments'}
        {include file=$oView->getTemplateFile('searchForm')}
        {include file=$oView->getTemplateFile('financeDetailsList')}
    {/if}
    </div>        
</div>   



