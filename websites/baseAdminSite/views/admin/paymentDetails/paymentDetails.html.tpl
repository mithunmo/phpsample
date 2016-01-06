{include file=$oView->getTemplateFile('paymentHeader', 'shared') pageTitle=''}
{include file=$oView->getTemplateFile('menu', 'shared')}

<div id="body">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>   
    <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1">
 
    <section class="tabsection" style="max-width: 1280px;">
         <h2 style="padding-top:20px;">Payments</h2>
        {if $oUser->getPermissions()->isRoot()}
            <div class="tabsmenu">
                <label {if $oController->getAction() == 'viewAccountManger' || $oController->getAction() == 'viewObjects'}id = 'tabactive' {else} id = 'tab1'{/if}><a href="/admin/paymentDetails/viewAccountManger">All Payments</a></label>
                <label {if $oController->getAction() == 'viewCompliance' }id = 'tabactive' {else} id = 'tab2'{/if}><a href="/admin/paymentDetails/viewCompliance" >Compliance</a></label>
                <label {if $oController->getAction() == 'duePayments' || $oController->getAction() == 'viewFinance' || $oController->getAction() == 'newPayments'}id = 'tabactive'{else}id = 'tab3'{/if}><a href="/admin/paymentDetails/viewFinance" >Finance</a></label>
            </div>
            {if $oController->getAction() == 'newPayments' || $oController->getAction() == 'duePayments' ||  $oController->getAction() == 'viewFinance'}
                <div class="tabscontent">
                    {include file=$oView->getTemplateFile('financeDetails')}
                </div>
            {else}
                <div class="tabscontent">
                    <div id="content1">
                        {include file=$oView->getTemplateFile('searchForm')}
                        {include file=$oView->getTemplateFile('paymentDetailsList')}
                    </div>
                </div>
            {/if}
        {elseif $oController->hasAuthority('paymentDetailsController.viewAccountManger')}  
            <div class="tabsmenu">
                <label {if $oController->getAction() == 'viewAccountManger' || $oController->getAction() == 'viewObjects'}id = 'tabactive' {else} id = 'tab1'{/if}><a href="/admin/paymentDetails/viewAccountManger">All Payments</a></label>
            </div>
            <div class="tabscontent">
                <div id="content1">
                    {include file=$oView->getTemplateFile('searchForm')}
                    {include file=$oView->getTemplateFile('paymentDetailsList')}
                </div>
            </div>
        {elseif $oController->hasAuthority('paymentDetailsController.viewCompliance')}
            <div class="tabsmenu">
                <label {if $oController->getAction() == 'viewCompliance' || $oController->getAction() == 'viewObjects'}id = 'tabactive' {else} id = 'tab2'{/if}><a href="/admin/paymentDetails/viewCompliance" >Compliance</a></label>
            </div>
            <div class="tabscontent">
                <div id="content1">
                    {include file=$oView->getTemplateFile('searchForm')}
                    {include file=$oView->getTemplateFile('paymentDetailsList')}
                </div>
            </div>
        {elseif $oController->hasAuthority('paymentDetailsController.viewFinance')}  
            <div class="tabsmenu">
                <label {if $oController->getAction() == 'viewAccountManger' || $oController->getAction() == 'viewObjects'}id = 'tabactive' {else} id = 'tab1'{/if}><a href="/admin/paymentDetails/">All Payments</a></label>
                <label {if $oController->getAction() == 'duePayments' || $oController->getAction() == 'viewFinance' }id = 'tabactive'{else}id = 'tab3'{/if}><a href="/admin/paymentDetails/viewFinance" >Finance</a></label>
            </div>
            {if $oController->getAction() == 'newPayments' || $oController->getAction() == 'duePayments' ||  $oController->getAction() == 'viewFinance'}
                <div class="tabscontent">
                    {include file=$oView->getTemplateFile('financeDetails')}
                </div>
            {else}
                <div class="tabscontent">
                    <div id="content1">
                        {include file=$oView->getTemplateFile('searchForm')}
                        {include file=$oView->getTemplateFile('paymentDetailsList')}
                    </div>
                </div>
            {/if}
        {/if}

    </section>
</div>         

{include file=$oView->getTemplateFile('paymentFooter', 'shared')}


