{include file=$oView->getTemplateFile('header', 'shared') pageTitle=$oMap->getDescription()}
{include file=$oView->getTemplateFile('menu', 'shared')}
{assign var=eventID value=$oController->getSearchParameter('EventID')}
{assign var=objects value=$oModel->getObjectList($offset, $limit, $eventID, false)}
{assign var=totalObjects value=$oModel->getTotalObjects()}

	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
                        
                        

			<div class="floatLeft sideBar">
				{if !isset($parentController)}{assign var=parentController value='admin'}{/if}
				{generatePath controller=$oMap->getUriPath() parent=$parentController}
			</div>

			<div class="floatLeft main">
                          				
					<h2>{t}Advance Payment{/t}</h2>
                                        <form id="adminFormData" name="formData" method="post" action="/admin/payment/showGrantForm" accept-charset="utf-8">
                                            <div class="daoAction">
                                                <button type="submit" name="search" value="{t}Search{/t}" class="floatRight">
                                                    <img src="{$themeicons}/32x32/search.png" alt="search" class="icon" />
                                                    {t}Search{/t}
                                                </button> 

                                                <button  value="Reset" name="Reset" type="reset" class="floatRight">
                                                    <img class="icon" alt="Undo changes" src="/themes/mofilm/images/icons/32x32/action-undo.png">
                                                    {t}Reset{/t}
                                                </button>
                                                <a href="/admin/payment">
                                                    <img src="{$themeicons}/32x32/other.png" alt="{t}View List{/t}" class="icon" />
                                                    {t}View List{/t}
                                                </a>
                                            </div>
                                            <div class="clearBoth"></div>
                                            <div class="filters">
                                                <input type="radio" name="PaymentType" value="Ad-hoc"  /> Ad-hoc Payment
                                                <input type="radio" name="PaymentType" value="Advance Grant" checked /> Advance Payment
                                            </div>
                                            <div class="filters">Event :
                                                {eventSelect id="eventList" name='EventID' selected=$searchEventID eventEndDateFilter="2014-10-01" class="valignMiddle long"}
                                            </div>    
                                            <div class="filters">Brand :
                                                {if $searchEventID}
                                                        {sourceSelect id="eventListSources" name='SourceID' selected=$searchSourceID eventID=$searchEventID class="valignMiddle string" user=$oUser}
                                                {else}
                                                        {sourceDistinctSelect id="eventListSources" name='SourceID' selected=$searchSourceID class="valignMiddle string" user=$oUser}       
                                                {/if}
                                            </div>
                                            <div class="filters">Film Maker  : <input type="text" name="UserName" /></div>
                                        </form>
					
				
			</div>

			<br class="clearBoth" />
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}