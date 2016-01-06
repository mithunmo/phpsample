{include file=$oView->getTemplateFile('header', 'shared') pageTitle='{t}Videos - Search{/t}'}
{include file=$oView->getTemplateFile('menu', 'shared')}
{assign var=searchCorporateID value=$oController->getCorporateQuery()}
{assign var=searchBrandID value=$oController->getBrandQuery()}
{assign var=searchProductID value=$oController->getProductQuery()}
	<div id="body">
		<div class="container">
			{include file=$oView->getTemplateFile('statusMessage', '/shared')}
			
			<h2>{t}Videos : Search{/t} </h2>
			
			<form name="videoSearch" id="videoSearch" action="{$daoUriView}" method="get">
				<div class="filters">
                                    <div style="width:100%;height:40px;padding-top:5px;padding-left:10px;">
                                        <div style="width:425px;height:50px;float:left;padding-top: 20px;">
                                           
                                            <input type="text" placeholder="Search by Keywords" name="Keywords" id='Keywords' value="{$searchKeywords|escape:'htmlall':'UTF-8'}" class="valignMiddle string" onfocus="this.select()" />
                                            <a href="#" id="contactUs">Assist Me in Tags</a>
                                            <div style="clear:both;width:425px;height:30px;padding-top:5px;">
                                            &nbsp;&nbsp;
						{t}Tags{/t} <input type="checkbox" name="Tags" id="onlyTags" value="1" {if $searchTags}checked="checked"{/if}/>

						{*
						&nbsp;&nbsp;
						{t}Only Favourites{/t} <input type="checkbox" name="Favourites" value="1" {if $searchFavourites}checked="checked"{/if}/>
						*}
						&nbsp;&nbsp;
						{t}Titles{/t} <input type="checkbox" name="onlyTitles" value="1" {if $searchOnlyTitles}checked="checked"{/if}/>
						&nbsp;&nbsp;
						{t}Display as List{/t} <input type="radio" name="Display" value="list" {if $searchDisplay == 'list'}checked="checked"{/if}/>
                                                {t}Grid{/t} <input type="radio" name="Display" value="grid" {if $searchDisplay == 'grid'}checked="checked"{/if}/>
                                            </div>
                                        </div>
                                        <div style="width:475px;height:50px;float:left;padding-right:10px;">
                                            <div style="float:right;width:100px;padding-top:3px;">
                                                <button type="submit" name="search" value="{t}Search{/t}" class="floatRight" >
                                                    <img src="{$themeicons}/32x32/search.png" alt="search" class="icon" />
                                                    {t}Search{/t}
                                                </button>
                                            </div>
                                            <div style="float:left;width:120px;">
                                                <strong>Awarded</strong><br/>
                                                {if $oController->hasAuthority('videosController.canSearchByAward')}
                                                        {movieAwardSelect name='Award' selected=$searchAward class="valignMiddle"}
                                                {/if} 
                                            </div>
                                            <div style="float:left;width:160px;">
                                                <strong>Legal</strong><br/>
                                                {if $oController->hasAuthority('videosController.canSearchByStatus')}
                                                        {movieStatusSelect name='Status' selected=$searchStatus class="valignMiddle"}
                                                {/if}
                                            </div>
                                        </div>
                                    </div>
                                    <div style="clear:both;width:100%;padding-bottom:2px;padding-left:10px;padding-top:10px;">
                                        <div style="float:left;">
                                                <strong>{t}Client{/t}</strong> <br/>
                                                {corporateSelect id="eventListCorporates" name='CorporateID' selected=$searchCorporateID class="valignMiddle" }
                                        </div>
                                        <div style="float:left; padding-left:15px;">
                                                <strong>{t}Brand</strong>{/t}<br/>
                                                {if $searchCorporateID}
                                                        {brandSelect id="corporateListBrands" name='BrandID' selected=$searchBrandID eventID=$searchEventID CorporateID=$searchCorporateID class="valignMiddle" }
                                                {else}
                                                        {brandDistinctSelect id="corporateListBrands" name='BrandID' selected=$searchBrandID  class="valignMiddle " }       
                                                {/if}
                                        </div>
                                        <div style="float:left;padding-left:15px;">
                                                <strong>{t}Project</strong>{/t}<br/>
                                                {if $oController->hasAuthority('videosController.canSearchByEvent')}
							{eventSelect id="eventListVideo" name='EventID' selected=$searchEventID CorporateID=$searchCorporateID BrandID=$searchBrandID  class="valignMiddle " user=$oUser}
						{/if}
                                        </div>
                                         <div style="float:left;padding-left:15px;">
                                                <strong>{t}Product</strong>{/t}<br/>
                                                {productDistinctSelect id="productListVideo" name='ProductID' selected=$searchProductID class="valignMiddle " }
						
                                        </div>
                                    </div>
                                    <div id="tagdialog" style="display:none;">
                                        <div>   
                                            <div style="padding-bottom:10px;">
                                                Please select tags you want to search and click on 'Select'.  Click 'Search' button on main screen to do the search.
                                            </div>
                                            <div style="text-align:right;border-radius: 5px;">
                                                <button type="submit" id="tagSubmitTop" name="search" value="{t}Select{/t}" class="floatRight">
                                                    <img src="{$themeicons}/32x32/search.png" alt="Select" class="icon" />
                                                    {t}Select{/t}
                                                </button>
                                            </div>
                                            {assign var=i value=1}
                                            {foreach $newGenres as $oTags}
                                            <div style="width:570px; height:25px; padding:4px;"><strong>{$oTags@key}</strong></div>
                                            <div style="padding:4px;width:570px; overflow:auto;" >
                                                            {foreach $oTags as $oTag}
                                                                    <div style="float:left; width:185px;">
                                                                            {if $oTag->getCategory() == "Industry"}
                                                               <input class="industry" type="checkbox" name="Tags[]" value="{trim($oTag->getName())}" />{$oTag->getName()}
                                                                            {else}
                                                                <input type="checkbox" name="Tags[]" value="{trim($oTag->getName())}"  />{$oTag->getName()}
                                                                            {/if}
                                                                    </div>

                                                            {/foreach}
                                            </div>
                                            {/foreach}
                                            <div style="text-align:right;border-radius: 5px;">
                                                 <button type="submit" id="tagSubmitBottom" name="search" value="{t}Select{/t}" class="floatRight" style="padding:10px;">
                                                    <img src="{$themeicons}/32x32/search.png" alt="Select" class="icon" />
                                                     {t}Select{/t}
                                                </button>
                                            </div>
                                       </div>
                                    </div>
                                    <div class="clearBoth" style="height:2px;"></div>
				</div>
			</form>
			
			{*assign var=offset value=$oModel->getOffset()|default:0*}
			{assign var=offset value=$oModel->getSolrVideoSearch()->getStart()|default:0}
			{assign var=limit value=30}
			{assign var=totalObjects value=$oResults->getTotalResults()}
			
			{*include file=$oView->getTemplateFile('videoResultList')*}
			{if $searchDisplay == 'list'}
				{include file=$oView->getTemplateFile('videoResultList')}
			{else}
				{include file=$oView->getTemplateFile('videoResultGrid')}
			{/if}
		</div>
	</div>

{include file=$oView->getTemplateFile('footer', 'shared')}
