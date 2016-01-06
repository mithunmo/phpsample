{assign var=offset value=$oController->getPagingOptions($pagingOffset)|default:0}
{assign var=daoSearchQuery value=$oController->getSearchQueryAsString()|default:''}
{assign var=statusFilter value=$oController->getStatusFilter()|default:''}
{assign var=filmMakerFilter value=$oController->getFilmMakerFilter()|default:''}
{assign var=limit value=20}
{assign var=objects value=$oModel->getObjectList($statusFilter,$filmMakerFilter,$offset, $limit)}
{assign var=totalObjects value=$oModel->getTotalObjects($statusFilter,$filmMakerFilter)}
<form id="adminFormData" name="formData" method="post" action="/admin/payment/" accept-charset="utf-8">
    <div style="padding-bottom: 15px;">
        <div style="float:right;">
        <button type="submit" name="search" value="{t}Search{/t}" class="floatRight">
            <img src="{$themeicons}/32x32/search.png" alt="search" class="icon" />
            {t}Search{/t}
        </button> 

        <button  value="Reset" name="Reset" type="reset" class="floatRight">
            <img class="icon" alt="Undo changes" src="/themes/mofilm/images/icons/32x32/action-undo.png">
            {t}Reset{/t}
        </button>
        </div>
        <div class="clearBoth"></div>
        <div style="margin-bottom:22px; padding:15px;">
            <div style="float:left;padding-left: 12px;">Status :
                <select name='Status'>
                    <option value=''>All</option>
                    <option value='Payment Made'>Payment Made</option>
                    <option value='Payment Approved'>Payment Approved</option>
                    <option value='Pending'>Pending</option>
                </select>
            </div>
            <div style="float:left;padding-left: 12px;">
                Film Maker :
                <input type="text" name="SearchFilmMaker" value="" style="width:130px;"/>
            </div>
             <div style="float:right;padding-left: 12px;">
                <a href="/admin/payment/searchGrant">
                    <img class="icon" alt="Undo changes" src="/themes/mofilm/images/icons/32x32/action-new-object.png">
                    {t}Ad-hoc/Advance Payment{/t}
                </a>
            </div>
        </div>
    </div>

</form>
					
{if $objects->getArrayCount() > 0}
	<table class="data">
		<thead>
			<tr>
				<th class="first" width="150">{t}User{/t}</th>
				<th>{t}Grant ID{/t}</th>
				<th>{t}Payment Type{/t}</th>
				<th>{t}Amount{/t}</th>
				<th>{t}Status{/t}</th>
                                <th></th>
                                <th></th>
				<th class="last">&nbsp;</th>
			</tr>
		</thead>
                <thead>
                  
			{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=6}
                    
		</thead>
		<tfoot>
                  
			{include file=$oView->getTemplateFile('daoPaging', '/shared') colspan=6}
                    
		</tfoot>
		<tbody>
		{foreach $objects as $oObject}
			<tr class="{cycle values="alt,"}">
                                <td><a href="/users/edit/{$oObject->getUserID()}">{$oModel->getUserName($oObject->getUserID())}</a></td>
                                <td>{if $oObject->getGrantID() == 0}{'-'}{else}<a href="/grants/view/{$oObject->getGrantID()}">{$oObject->getGrantID()}</a>{/if}</td></td>
				<td>{$oObject->getPaymentType()}</td>
				<td>{if $oObject->getAmountGrant() != ''}${$oObject->getAmountGrant()}{/if}</td>		
				<td>{$oObject->getStatus()}</td>
                                <td><a href="/admin/payment/paymentDetails/{$oObject->getID()}">View</a></td>
                                {if $oObject->getStatus() != 'Rejected'}
                                    {if $oObject->getStatus() == 'Pending'}
                                        <td><a href="/admin/payment/changeStatus/{$oObject->getID()}">Approve</a></td>
                                    {elseif $oObject->getStatus() == 'Payment Approved'}
                                        <td><a href="/admin/payment/paymentDone/{$oObject->getID()}">Update</a></td>
                                    {else}
                                        <td>-</td>
                                    {/if}    
                                {else}
                                    <td>-</td>
                                {/if}    
				<td class="actions">
					{include file=$oView->getTemplateFile('daoObjectListDefaultActions', '/shared')}
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
{else}
	<p>{t}No objects found in system.{/t}</p>
{/if}
