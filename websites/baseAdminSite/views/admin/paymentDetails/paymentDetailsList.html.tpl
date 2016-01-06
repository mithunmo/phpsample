<table class="payformtable" style="width:100%; overflow:auto;">
        <thead>
            <tr>
                <th style="width:12%;">ID</th>
                <th style="width:12%;">Project</th>
                <th style="width:12%;">Brand</th>
                <th style="width:12%;">Filmmaker</th>
                <th style="width:8%;">Type</th>
                <th style="width:8%;">Status</th>
                <th style="width:10%;">Amount</th>
                <th style="width:12%;">Created on</th>
                <th style="width:15%;">Due on</th>
                <th style="width:8%;">Paid on</th>
                <th style="width:2%;">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            {foreach $oPaymentList['paymentList'] as $payment }
            <tr>
                <td >#{$payment['payment']['ID']}</td>
                <td>
                    <h5>{$payment['events']['name']}</h5>
                    <h6>{$payment['products']['name']}</h6>
                </td>
                <td>{$payment['brands']['name']}</td>
                <td><a href="/users/edit/{$payment['users']['ID']}">{$payment['users']['firstname']}{' '}{$payment['users']['surname']}</a>
                </td>
                <td>{$payment['payment']['paymentType']}</td>
                <td>{$payment['payment']['status']}</td>
                <td class="tdamount">{'$'}{$payment['payment']['payableAmount']}</td>
                <td>{$payment['payment']['created']|date_format:'%m/%d/%Y'}</td>
                {if ($payment['payment']['dueDate']|date_format:'%Y-%m-%d') == date('Y-m-d') || date('Y-m-d',strtotime("+1 day", time())) == ($payment['payment']['dueDate']|date_format:'%Y-%m-%d')}
                    <td class="dueonToday">{$payment['payment']['dueDate']|date_format:'%m/%d/%Y'}</td>
                {else}
                    {if strtotime($payment['payment']['dueDate']) < strtotime(date('Y-m-d'))}
                        <td class="dueonpast">{$payment['payment']['dueDate']|date_format:'%m/%d/%Y'}</td>
                    {else}
                        <td class="dueon">{$payment['payment']['dueDate']|date_format:'%m/%d/%Y'}</td>
                    {/if}
                {/if}
                <td>{$payment['payment']['paidDate']|date_format:'%m/%d/%Y'}</td>
                <td style="min-width:75px;">
                    <a href="/admin/paymentDetails/edit/{$payment['payment']['ID']}?{$oController->getCallBackQuery()}"><img src="/themes/mofilm/images/payment/editicn.jpg" alt="edit" /></a>                   
                    <a href="/admin/paymentDetails/edit/{$payment['payment']['ID']}?{$oController->getCallBackQuery()}" style="margin:0px 5px; vertical-align: top;">Detail</a>                 
                </td>
            </tr>
           {/foreach}
        </tbody>
</table>
{if $oPaymentList['paymentPagination']['TotalRows'] > $oPaymentList['paymentPagination']['RowsLimit']}       
    <div class="paynavigation">
            {assign var=nextPage value=($oPaymentList['paymentPagination']['PageNo'] + 1)}
            {assign var=prevPage value=($oPaymentList['paymentPagination']['PageNo'] - 1)}
            {assign var=lastPage value=(ceil($oPaymentList['paymentPagination']['TotalRows']/$oPaymentList['paymentPagination']['RowsLimit']))}

            {if $oPaymentList['paymentPagination']['PageNo'] == 1 }
                <a class="first" ></a>
                <a class="previous" ></a>
            {else}
                <a class="first" href="/admin/paymentDetails/{$oController->getAction()}?{$rawDaoSearchQuery}&PageNo=1&RowsLimit={$oPaymentList['paymentPagination']['RowsLimit']}"></a>
                <a class="previous" href="/admin/paymentDetails/{$oController->getAction()}?{$rawDaoSearchQuery}&PageNo={$prevPage}&RowsLimit={$oPaymentList['paymentPagination']['RowsLimit']}"></a>
            {/if}
            {if $lastPage == $oPaymentList['paymentPagination']['PageNo'] }
                <a class="next" ></a>
                <a class="last" ></a>
            {else}
                <a class="next" href="/admin/paymentDetails/{$oController->getAction()}?{$rawDaoSearchQuery}&PageNo={$nextPage}&RowsLimit={$oPaymentList['paymentPagination']['RowsLimit']}"></a>
                <a class="last" href="/admin/paymentDetails/{$oController->getAction()}?{$rawDaoSearchQuery}&PageNo={$lastPage}&RowsLimit={$oPaymentList['paymentPagination']['RowsLimit']}"></a>
            {/if}
    </div>
{/if}
