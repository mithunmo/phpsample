{include file=$oView->getTemplateFile('header', 'shared') pageTitle=$oMap->getDescription()}
{include file=$oView->getTemplateFile('menu', 'shared')}
	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
                        
                        

			<div class="floatLeft sideBar">
				{if !isset($parentController)}{assign var=parentController value='admin'}{/if}
				{generatePath controller=$oMap->getUriPath() parent=$parentController}
			</div>

			<div class="floatLeft main">
                          
				
					<h2>{t}Advance Payment Form{/t}</h2>
                                        <form id="advanceGrantForm" name="advanceGrantForm" method="post" action="/admin/payment/doAdvanceGrant" accept-charset="utf-8">
		                        <div class="content">
						<div class="daoAction">
                                                        <a href="/admin/payment">
                                                            <img src="{$themeicons}/32x32/other.png" alt="{t}View List{/t}" class="icon" />
                                                            {t}View List{/t}
                                                        </a>
                                                        <a href="/admin/payment/searchGrant">
                                                            <img class="icon" alt="New" src="/themes/mofilm/images/icons/32x32/action-new-object.png">
                                                            {t}New{/t}
                                                        </a>
							{if $oController->hasAuthority('usersController.doEdit')}
							<button type="submit" name="UpdateProfile" value="Save" title="{t}Save Changes{/t}">
								<img src="{$themeicons}/32x32/action-do-edit-object.png" alt="{t}Save Changes{/t}" class="icon" />
								{t}Save Changes{/t}
							</button>
							{/if}
                                                       
						</div>
						<div class="clearBoth"></div>
					</div>
                                        <div class="filters">
                                            {if $requestParam['PaymentType'] eq 'Ad-hoc'} 
                                                <input type="radio" name="PaymentType" value="Ad-hoc"  checked/> Ad-hoc Payment
                                                <input type="radio" name="PaymentType" value="Advance Grant" disabled="disabled" /> Advance Payment
                                            {else} 
                                                <input type="radio" name="PaymentType" value="Ad-hoc" disabled="disabled" /> Ad-hoc Payment
                                                <input type="radio" name="PaymentType" value="Advance Grant"  checked/> Advance Payment
                                            {/if} 
                                         
                                            
                                        </div>
                                        <div class="filters">Event :
                                            {eventSelect id="eventList" name='EventIDHidden' selected=$requestParam['EventID'] class="valignMiddle long" disabled="disabled"}
                                        </div>    
                                        <div class="filters">Brand :
                                            {if $requestParam['EventID']}
                                                    {sourceSelect id="eventListSources" name='SourceIDHidden' selected=$requestParam['SourceID'] eventID=$requestParam['EventID'] class="valignMiddle string" user=$oUser disabled="disabled"}
                                            {else}
                                                    {sourceDistinctSelect id="eventListSources" name='SourceIDHidden' selected=$requestParam['SourceID'] class="valignMiddle string" user=$oUser disabled="disabled"}       
                                            {/if}
                                        </div>
                                        <div class="filters">Film Maker  : <input type="text" name="UserName" value='{$requestParam['UserName']}' readonly="readonly" /></div>
                                        
                                        <div>
                                            <h3>{t}Select Film Maker{/t}</h3>
                                             <table class="data">                                              
                                                {if !empty($grantList)} 
                                                    {if $requestParam['PaymentType'] == 'Advance Grant'} 
                                                        <thred>
                                                        <tr>
                                                            <th></th>
                                                            <th>Film Maker</th>
                                                            <th>Email</th>
                                                            <th>Granted Amount</th>
                                                            <th>Advance Given</th>
                                                        </tr>
                                                        </thred>
                                                        {foreach name=outer item=grantListRow from=$grantList}
                                                            {assign var=advanceGrant value=$oModel->checkGrantExists($grantListRow.UserID,$grantListRow.GrantID,$grantListRow.GrantedAmount)}
                                                            {if $advanceGrant == '-'}
                                                                {assign var=substructedAmount value =$grantListRow.GrantedAmount}
                                                            {else}
                                                                {assign var=substructedAmount value =($grantListRow.GrantedAmount - substr($advanceGrant, 1))}
                                                            {/if}
                                                            <tr>
                                                                <td><input type='radio' name="UserID" value="{$grantListRow.UserID}-{$substructedAmount}-{$grantListRow.GrantID}" /></td>
                                                                <td>{$grantListRow.firstname} {$grantListRow.surname}</td>
                                                                <td>{$grantListRow.email}</td>
                                                                <td>${$grantListRow.GrantedAmount}</td> 
                                                                <td>{$advanceGrant}</td>
                                                            </tr>
                                                        {/foreach}   
                                                    {else}
                                                        <thred>
                                                        <tr>
                                                            <th></th>
                                                            <th>Film Maker</th>
                                                            <th>Email</th>
                                                            <th></th>
                                                            <th></th>
                                                        </tr>
                                                        </thred>
                                                        {foreach name=outer item=grantListRow from=$grantList}
                                                        <tr>
                                                            <td><input type='radio' name="UserID" value="{$grantListRow.UserID}" /></td>
                                                            <td>{$grantListRow.firstname} {$grantListRow.surname}</td>
                                                            <td>{$grantListRow.email}</td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                        {/foreach}
                                                      
                                                    {/if}
                                                    <tr>
                                                        <td>Enter Advance Amount ($): </td>
                                                        <td colspan="4"><input type="text" name="AdvanceAmount"  value="" /></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Enter Comments : </td>
                                                        <td colspan="4"><textarea name="Comments"  cols="70" rows="5" ></textarea></td>
                                                    </tr>
                                                {else}
                                                    <tr><td colspan="45">No Records Avaiable.</td></tr>
                                                {/if}
                                                <input type="hidden" name="UserName" value="{$requestParam['UserName']}"/>
                                                <input type="hidden" name="EventID" value="{$requestParam['EventID']}" />
                                                <input type="hidden" name="SourceID" value="{$requestParam['SourceID']}" />
                                            </table>
                                            
                                        </div>
                                           
                                        </form>
					
				
			</div>

			<br class="clearBoth" />
		</div>
	</div>
<script>
    function maxValCheck(paramName,grantedAmount){
        if($('#advanceGrant'+paramName).val() != ''){
            if($('#advanceGrant'+paramName).val() <= parseInt(grantedAmount)){
                $('#advanceGrantForm').submit();
            }else{
                alert('Advance amount should not limit the granted amount of '+grantedAmount);
            }
        }else{
            alert('Give the amount for Advance payment');
        }
    }
</script>
{include file=$oView->getTemplateFile('footer', 'shared')}