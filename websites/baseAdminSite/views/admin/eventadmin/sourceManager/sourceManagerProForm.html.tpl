<script type="text/javascript" src="https://www.mofilm.com/js/jquery.min.js"></script>
<script type="text/javascript" src="https://www.mofilm.com/js/jqueryautocomplete/jquery.ui.autocomplete.html.js"></script>
<script src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script>
    $(document).ready(function () {
         var FMID=[
    {foreach name=i from=$FM item=v}
      '{$v|escape:"javascript"}'
      {if !$smarty.foreach.i.last},{/if}
    {/foreach}
    ];
    
        var FMNAME=[
    {foreach name=i from=$FMNAME item=v}
      '{$v|escape:"javascript"}'
      {if !$smarty.foreach.i.last},{/if}
    {/foreach}
    ];
        var cnt = $('#contributors tbody tr').length;
        var length = {$length}
         for (i = 2; i <= length; i++)
        {

            cnt++;
            var newEles = $(
                        '<tr style="background:none">' +
                        '<td width="70%" style="padding: 0px;"><div class="ui-widget"><input style="font-family:Apercu Regular,Calibri,sans-serif;font-size:14px;" placeholder="Name" type="text" class="contributorRole small" id="Contributors[' + cnt + ']" name="Contributors[' + cnt + ']" value="'+FMNAME[cnt-1]+'"     /> </div></td>' +
                        '<td  width="10%"></td>' +
                        '<td  width="8%"> <div style="width: 20px;margin-left: 20px;margin-top: -17px;" class="removeCurContributor formIcon ui-state-default floatLeft"  title="Remove this contributor"><span class="ui-icon ui-icon-minusthick" ></span></div></td>'+
                        '<td  width="2%"></td> <input type="hidden" id="FM[' + cnt + ']"  name="FM[' + cnt + ']" value="'+FMID[cnt-1]+'"></tr>'
                        );
            
            
            newEles.appendTo('#contributors tbody');
            newEles.find('.contributorRole').autocomplete(acOptions);

            newEles.find('div.removeCurContributor').click(function () {
                $(this).parents('#contributors tr').remove();
                $('#contributors span.recordNumber').text(function (index) {
                    return index + 1;
                });
            });

        }
        
                var acOptions = {
                source: "/video/getUsers",
                minLength: 1,
                select: function (event, ui) {
                    $(this).parent().parent().next().next().find("input").val(ui.item.key)
                    var Cont_name = $(this).attr('name');
                    var index = Cont_name.replace("Contributors","");
                    var name = "FM"+index;
                    //var index = Cont_name.substr(Cont_name.length - 3);
                    var name = "FM"+index;
                    $('input[name="'+name+'"]').val(ui.item.key);
                    
                    },
                change: function (event, ui) {
                    if (!ui.item) { 
                        $(this).val("");
                        $('#body div.container').append('<div id="formErrorBox" class="messageBox error"><p> Invalid Name</p></div>');
                        $('#body div.container div.messageBox').delay(4500).slideUp(200);
                        return false;
                    } else {
                        var Cont_name = $(this).attr('name');
                        $(this).parent().parent().next().next().find("input").val(ui.item.key)
                    }
                },
                html: true

            };


            $('input.contributorRole').autocomplete(acOptions);



            $('div.controls').show();

            $('body').on('keydown', 'input.contributorRole', function (e) {
                var key = e.which;
                if (key == 13) {
                    AddInput();
                    return e.which !== 13;

                }
            });

            $('div.addContributor').on('click', function () {
                AddInput(); 
            });

            function AddInput() {
                 cnt++;
                 var newEles = $(
                        '<tr style="background:none">' +
                        '<td width="70%" style="padding: 0px;"><div class="ui-widget"><input style="font-family:Apercu Regular,Calibri,sans-serif;font-size:14px;" placeholder="Name" type="text" class="contributorRole small" id="Contributors[' + cnt + ']" name="Contributors[' + cnt + ']" value=""    /> </div></td>' +
                        '<td  width="10%"></td>' +
                        '<td  width="8%"> <div style="width: 20px;margin-left: 20px;margin-top: -17px;" class="removeCurContributor formIcon ui-state-default floatLeft"  title="Remove this contributor"><span class="ui-icon ui-icon-minusthick" ></span></div></td>'+
                        '<td  width="2%"></td> <input type="hidden" id="FM[' + cnt + ']"  name="FM[' + cnt + ']"></tr>'
                        );

                newEles.appendTo('#contributors tbody');
                newEles.find('.contributorRole').autocomplete(acOptions);

                newEles.find('div.removeCurContributor').click(function () {
                    $('#skills_error').css("display", "none");
                    $(this).parents('#contributors tr').remove();
                    $('#contributors span.recordNumber').text(function (index) {
                        return index + 1;
                    });
                });
            }

            $('input[type=checkbox].addRemoveControl').replaceWith(
                    '<div class="removeCurContributor formIcon ui-state-default floatLeft" title="Remove this contributor"><span class="ui-icon ui-icon-minusthick"></span></div>'
                    );

            $('div.removeContributor').click(function () {
                $('#contributors tbody tr').last().remove();
            });

            $('div.removeCurContributor').click(function () {
                $(this).parents('#contributors tr').remove();
                $('#contributors span.recordNumber').text(function (index) {
                    return index + 1;
                });
                formChangedWarningBox();
            });
        
    });
                  
</script>

{if $oController->getAction() == 'editObject' || $oController->getAction() == 'newObject'}
    {assign var=sourcePrimaryID value=$oController->getPrimaryKey()|default:0}  
    {assign var=searchCorporateID value=$oController->getCorporate($oObject->getBrandID())}
    <div class="hidden">
        <input type="hidden" name="PrimaryKey" value="{$sourcePrimaryID}" />
        <input type="hidden" name="Name" id='brandName' value="{$oObject->getName()}" class="string" /></td>
    <input type="hidden" name="BrandID" id='brandID' value="{$oObject->getBrandID()}" class="string" /></td>   
<input type="hidden" name="SourceDataSetID" value="{$oObject->getSourceDataSet()->getID()|default:0}" />
<input type="hidden" name="GrantID" value="{$oObject->getGrants()->getID()|default:0}" />
<input type="hidden" name="Hash" value="{$oObject->getSourceDataSet()->getHash()|default:0}" />
</div>
<div class="content">	
    <div class="daoAction">
        {if $oObject->getSourceDataSet()->getHash()}
            <a href="http://www.mofilm.com/competitions/previewproBrand/{$oObject->getSourceDataSet()->getHash()}" title="{t}Preview{/t}" target="_blank">
                <img src="/themes/shared/icons/email.png" alt="{t}Preview{/t}" class="icon" />
                {t}Preview{/t}
            </a>
        {/if}
        {*<a href="#?height=450&width=900&modal=false" title="{t}Publish{/t}" id="" class="thickbox">
        <img src="/themes/shared/icons/email.png" alt="{t}Publish{/t}" class="icon" />
        {t}Publish{/t}
        </a>*}
    </div>
    <div class="clearBoth"></div>
</div>
<div id="userFormAccordion">
    <h3 id="branddetails"><a href="#">Add Particpating Brand to a Project</a></h3>
    <div>
        <table class="data">
            <tbody>

                <tr>
                    <th>{t}Project name{/t}</th>
                    <td>{eventSelect id="projectEventID" name='EventID' selected=$oObject->getEventID()}</td>
                </tr>
            </tbody></table></div>    

    <h3><a href="#">Select Brand</a></h3>
    <div>
        <table class="data">
            <tbody>
                <tr>
                    <th>{t}Corporate{/t}</th>
                    <td>{corporateDistinctSelect id="eventListCorporates" name='CorporateID' selected=$searchCorporateID class="valignMiddle string" }</td>
                </tr>
                <tr>
                    <th>{t}Brand{/t}</th>
                    <td>
                        {if $searchCorporateID}
                            {brandSelect id="corporateListBrands" name='BrandIDSelect' selected=$oObject->getBrandID() corporateID=$searchCorporateID class="valignMiddle string" }
                        {else}
                            {brandDistinctSelect id="corporateListBrands" name='BrandIDSelect' selected=$oObject->getBrandID() class="valignMiddle string" }       
                        {/if}
                    </td>
                </tr>
            </tbody></table></div>

    <h3><a href="#">Project/ Brand Sponsor</a></h3>
    <div>
        <table class="data">
            <tbody>

                <tr>                                            
                    <th>{t}MOFILMer{/t}</th>
                    <td>
                        <select id="brandSponsorID" name="SponsorID">                                                
                            <option value=""> Select the user</option>                                             
                            {foreach $userList as $oAdminUser}                                                
                                <option {if $oObject->getSponsorID() == $oAdminUser->getID()}selected="{$oAdminUser->getID()}" {/if} value="{$oAdminUser->getID()}">{$oAdminUser->getFullname()} </option>                                             
                            {/foreach}
                        </select>    
                    </td>
                </tr>    
            </tbody></table>          
    </div>
    {*            
    <h3><a href="#">Brand Details</a></h3>
    <div>
    <table class="data">
    <tbody>
    <tr>
    <th>{t}Corporate Name{/t}</th>
    <td>{corporateDistinctSelect id="eventListCorporates" name='CorporateID' selected=$searchCorporateID class="valignMiddle string" }</td>
    </tr>
    <tr>
    <th>{t}Brand Name{/t}</th>
    <td>
    {if $searchCorporateID}
    {brandSelect id="corporateListBrands" name='BrandIDSelect' selected=$oObject->getBrandID() corporateID=$searchCorporateID class="valignMiddle string" }
    {else}
    {brandDistinctSelect id="corporateListBrands" name='BrandIDSelect' selected=$oObject->getBrandID() class="valignMiddle string" }       
    {/if}
    </td>
    </tr>
    <tr>
        
    <th>{t}Sponsor{/t}</th>
    <td>
    <select name="SponsorID">                                                
    <option value=""> Select the user</option>                                             
    {foreach $userList as $oAdminUser}                                                
    <option {if $oObject->getSponsorID() == $oAdminUser->getID()}selected="{$oAdminUser->getID()}" {/if} value="{$oAdminUser->getID()}">{$oAdminUser->getFullname()} </option>                                             
    {/foreach}
    </select>    
    </td>
    </tr>    
    <tr>
    <th>{t}Project{/t}</th>
    <td>{eventSelect id="projectEventID" name='EventID' selected=$oObject->getEventID()}</td>
    </tr>
    </tbody>
    </table>
    </div>
    *}                
    <h3><a href="#">Splash Page and Visibility</a></h3>
    <div>
        <table class="data">
            <tbody>

                <tr>
                    <th>{t}Public{/t}</th>
                        {*<td>{yesNoSelect name='Hidden' selected=$oObject->getHidden()}</td>*}
                    <td>
                        {if $oController->getAction() == 'newObject'}
                            <select id="public" name="Hidden">
                                <option selected value="Y">No</option>>                        
                                <option  value="N">Yes</option>>      
                                <option  value="I">Invite Only</option>>
                            </select>

                        {else}
                            <select name="Hidden">
                                <option {if $oObject->getHidden() == "Y"}selected={$oObject->getHidden()} {/if} value="Y">No</option>>                        
                                <option {if $oObject->getHidden() == "N"}selected={$oObject->getHidden()} {/if} value="N">Yes</option>>                        
                                <option {if $oObject->getHidden() == "I"}selected={$oObject->getHidden()} {/if} value="I">Invitation Only</option>>                        
                            </select>

                        {/if}    
                    </td>
                </tr>

                <tr>
                    <th>{t}Custom design{/t}</th>
                    <td>{yesNoSelect name='Custom' selected=$oObject->getCustom()}</td>
                </tr>                                        
                <tr><th></th>
                    <td>{t}When are film uploads and ( if public) splash pages visible for this project?{/t}</td>
                </tr>                                        
                <tr><th></th>
                    <td></td>
                </tr>                                        

                <tr>
                    <th>{t}Open{/t}</th>
                    <td>
                        {t}Use Project Submission Start Date{/t}
                        <input class="sourceManagerDate" type="checkbox" name="UseEventStartDate" title="{t}Tick to use the event Start Date{/t}" value="1" {if !$oObject->getStartDate()}checked="checked"{/if} />
                        <br />

                        <input type="text" name="Startdate" value="{$oObject->getStartDate()|date_format:'%Y-%m-%d'}" class="date datepicker" />
                        <strong>@</strong>
                        {html_select_time field_array='StartdateTime' prefix='' display_seconds=false minute_interval=10 time=$oObject->getStartDate()}
                    </td>
                </tr>
                <tr>
                    <th>{t}Closed{/t}</th>
                    <td>
                        {t}Use Project Submission End Date{/t}
                        <input class="sourceManagerDate" type="checkbox" name="UseEventEndDate" title="{t}Tick to use the event End Date{/t}" value="1" {if !$oObject->getEndDate()}checked="checked"{/if} />
                        <br />

                        <input type="text" name="Enddate" value="{$oObject->getEndDate()|date_format:'%Y-%m-%d'}" class="date datepicker" />
                        <strong>@</strong>
                        {if $oObject->getEndDate() != ''}
                            {assign var=sourceEndDate value=$oObject->getEndDate()}
                        {else}
                            {assign var=sourceEndDate value=date('Y-m-d 23:50:00')}
                        {/if}
                        {html_select_time field_array='EnddateTime' prefix='' display_seconds=false minute_interval=10 time=$sourceEndDate}
                    </td>
                </tr>
                <tr>
                    <th>{t}Page title{/t}</th>
                    <td><input type="text" name="DisplayName" value="{$oObject->getSourceDataSet()->getName()|escape:'htmlall':'UTF-8'}" class="string" /></td>
                </tr>
                <tr>
                    <td colspan="5"><br />
                        <b>{t}Sitecopy{/t}</b><br /><br />
                        <textarea name="Sitecopy" rows="20" cols="40" class="tinymce">{$oObject->getSourceDataSet()->getDescription()|escape:'htmlall':'UTF-8'}</textarea>
                        <br /></td>
                </tr>
                <tr>
                    {*<th>{t}Terms{/t}</th>
                    <td>{termsSelect name='TermsID' selected=$oObject->getTermsID() title='Use Event Terms'}</td>*}
                    <td colspan="5"><br />
                        <b>{t}How it works?{/t}</b><br /><br />
                        <textarea name="Terms" rows="20" cols="40" class="tinymce">{$oObject->getSourceDataSet()->getTerms()|escape:'htmlall':'UTF-8'}</textarea>
                        <br /></td>
                </tr>
                <tr>
                    <th>{t}Instructions{/t}</th>
                    <td>
                        <textarea name="Instructions" rows="5" cols="60">{$oObject->getInstructions()|escape:'htmlall':'UTF-8'}</textarea>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
       
                   <h3><a href="#">User Permission</a></h3>
        <div >
            Who can see the splash page or make submissions to this project? <br>
            <span> 
                {if $oController->getAction() == 'editObject'}
                <input type='checkbox' name='AllUser'  {if $ALLPer == 1}checked {/if}> 
                {else}
                    <input type='checkbox' name='AllUser'  checked > 
                {/if}
                </span>
             Project permission  
            <br> <br>
                      <div>
                         <span style="font-weight: bold;"> Or choose: </span>
                         <div class="addContributor formIcon ui-state-default floatRight"  style="margin: 5px; text-align:center; font-size:14px; border-radius:13px; padding:5px; width:60px; background: none repeat scroll 0% 0%  #164f86; color:white;" >Add </div> 
                         <br>
                         <table id="contributors" style="background: none; border: none;" id="contributors">
                            <tbody>
                                <tr style="background:none"> {assign var="index" value="1"}                              
                                    <td width="70%" style="padding: 0px;"><div class="ui-widget" ><input  style="font-family:Apercu Regular,Calibri,sans-serif;font-size:14px;" placeholder="Name" type="text" class="contributorRole small" name="Contributors[{$index}]" value="{$FMNAME[$index]}"  id="EnterRole" /></div></td>
                                    <td width="10%"> </td>
                                    <td width="8%"> <div style="width: 20px;margin-left: 20px;margin-top: -17px;" class="removeCurContributor formIcon ui-state-default floatLeft"  title="Remove this contributor"><span class="ui-icon ui-icon-minusthick" ></span></div>
                                    </td>
                                    <td width="2%"></td>
                                    <input type="hidden"  value="{$FM[$index]}" name="FM[{$index}]" >
                                </tr>

                              </tbody>
                        </table> 
                      </div>
                </div>  
                    
    {*                
    <h3><a href="#">Brand Prize</a></h3>
    <div>
    <div>
    {t}Winner Trip Budget in $ {/t} <input type="text" name="Tripbudget" value="{$oObject->getTripbudget()|default:6000}" class="small" />
    </div>
    <table id="prizeData" class="data">
    <thead>
    <tr>
    <th>Position</th>
    <th>Amount in $</th>
    <th>Display Description</th>
    <th></th>
    </tr>
    </thead>
    <tbody>
    {foreach $oObject->getSourcePrizeSet() as $oPrize}
    <tr>
    <td>{$oPrize->getPosition()}</td>
    <td>{$oPrize->getAmount()}</td>
    <td>{$oPrize->getDescription()}</td>
    <td>
    <div class="removeCurPrize formIcon ui-state-default floatLeft" title="Remove Prize" id="{$oPrize->getID()}">
    <span class="ui-icon ui-icon-minusthick">
    </span>
    </div>
    <!--							<input type="hidden" name="Prize[{$index1 = $oPrize@iteration}{$index1}][ID]" value="{$oPrize->getID()}" />
    <input type="checkbox" name="Prize[{$index1}][Remove]" value="1" class="addRemovePrizeControl" />-->
    </td>
    </tr>
    {/foreach}
    <tr>
    <td><input type="hidden" name="Prize[{$index1+1}][ID]" value="0" />
    <input type="text" class="prizePosition small" name="Prize[{$index1+1}][Position]" value="" />
    </td>
    <td><input type="text" class="prizeAmount small" name="Prize[{$index1+1}][Amount]" value="" /></td>
    <td><textarea class="prizeDescription" name="Prize[{$index1+1}][Description]" rows="1" cols="40"></textarea></td>
    {*<td><input type="text" class="prizeDescription medium" name="Prize[{$index1+1}][Description]" value="" /></td>
    <td>
    <div class="removeCurPrize formIcon ui-state-default floatLeft" title="Remove Prize" id="new">
    <span class="ui-icon ui-icon-minusthick">
    </span>
    </div>
    </td>
    </tr>
    </tbody>
    </table>
    <div class="prizeControls hidden">
    <div class="floatRight">
    <div class="addPrize formIcon ui-state-default floatLeft" title="{t}Add New Prize{/t}"><span class="ui-icon ui-icon-plusthick"></span></div>
    {*<div class="removePrize formIcon ui-state-default floatLeft" title="{t}Remove Last Prize{/t}"><span class="ui-icon ui-icon-minusthick"></span></div>
    </div>
    <div class="clearBoth"></div>
    </div>
    </div>
    *}               
    {if $oController->getAction() == 'editObject'}
        <h3><a href="#">Design Images</a></h3>
        <div>
            <table class="data">
                <tbody>
                    <tr>
                        <th class="valignTop">
                            {t}Upload Logo{/t}<br />
                            <img src="{$adminEventFolder}/{$oObject->getLogoName()}.jpg" width="50" height="28" alt="{$oObject->getName()}" title="{$oObject->getName()}" class="valignMiddle" style="border: 1px solid #000;" />
                        </th>
                        <td colspan="4">
                            <input type="file" id="SourceLogo" name="SourceLogo" class="string" /><br />
                            {t}Please note:{/t}<br />
                            <em>
                                {t}Logos will be uploaded using the source name without any punctuation or special characters.{/t}<br />
                                {t}Logos will be resized to 261x139 (client) and 50x28 (admin) in JPEG format.{/t} 
                            </em>
                        </td>
                    </tr>
                    <tr>
                        <th class="valignTop">
                            {t}Upload Banner{/t}<br />
                        </th>
                        <td colspan="4">
                            <img src="{$adminEventFolder}/{$oObject->getLogoName()}.jpg" width="50" height="28" alt="{$oObject->getName()}" title="{$oObject->getName()}" class="valignMiddle" style="border: 1px solid #000;" />
                            <br />
                            <input type="file" name="SourceBanner" id="SourceBanner" class="string" />
                            <br />
                        </td>
                    </tr>
                    {*
                    <tr>
                    <th class="valignTop">
                    {t}Upload Filler{/t}<br />
                    </th>
                    <td colspan="4">
                    <img src="{$adminEventFolder}/{$oObject->getLogoName()}.jpg" width="50" height="28" alt="{$oObject->getName()}" title="{$oObject->getName()}" class="valignMiddle" style="border: 1px solid #000;" />
                    <br />
                    <input type="file" name="SourceFiller" id="SourceFiller" class="string" />
                    <br />
                    </td>
                    </tr>
                    <tr>
                    <th class="valignTop">
                    {t}Background Color{/t}<br />
                    </th>
                    <td colspan="4">
                    <br />
                    <input type="text" name="SourceBgcolor" value="{$oObject->getBgcolor()}" class="small string" />
                    <br />
                    </td>
                    </tr>
                    *}
                </tbody>
            </table>
        </div>
    {/if}
    <h3><a href="#">Ideas</a></h3>
    <div>
        <table class="data">
            <tbody> 
                <tr>
                    <th>{t}Ideas phase ?{/t}</th>

                    <td>
                        <select id="grantsAvailable" name="GrantsAvailability">
                            <option value="Y" {if $oObject->getGrants()->getID() > 0 }selected{/if}>Yes</option>
                            <option value="N" {if !($oObject->getGrants()->getID() > 0) }selected{/if}>No</option>
                        </select>
                    </td>
                </tr>

                <tr class="grantsTab" style="display:{if $oObject->getGrants()->getID() == 0 }none{/if}">
                    <th>{t}Deadline	{/t}</th>
                    <td>
                        <input type="text" name="GrantsDeadline" value="{$oObject->getGrants()->getEndDate()->getDate()|date_format:'%Y-%m-%d'}" class="date datepicker" />
                        <strong>@</strong>
                        {html_select_time field_array='GrantsDeadlineTime' prefix='' display_seconds=false minute_interval=10 time=$oObject->getGrants()->getEndDate()->getTime()}
                    </td>
                </tr>

                <tr class="grantsTab" style="display:{if $oObject->getGrants()->getID() == 0 }none{/if}">
                    {*<th>{t}Idea Pot Size in ${/t}</th> *}
                    <td>
                        <input type="hidden" name="CurrencySymbol" value="$" class="small" />
                        <input type="hidden" name="maxGrants" value="0" class="small" />
                    </td>
                </tr>					
                <tr class="grantsTab" style="display:{if $oObject->getGrants()->getID() == 0 }none{/if}">
                    <th>{t}Ideas Description{/t}</th>
                    <td>
                        <textarea name="GrantsDescription" rows="5" cols="60">{$oObject->getGrants()->getDescription()|escape:'htmlall':'UTF-8'}</textarea>
                    </td>
                </tr>

                {if $oObject->getGrants()->getID() > 0}
                    <tr class="grantsTab" style="display:{if $oObject->getGrants()->getID() == 0 }none{/if}">
                        <th>{t}Grants Link Button{/t}</th>
                        <td>
                            <input type="text" name="grantsLink" value="https://mofilm.com/accounts/grants/apply/{$oController->getPrimaryKey()}" class="long" onclick="this.focus();
                                                                        this.select();" readonly="readonly" />
                        </td>
                    </tr>
                {/if}

            </tbody>
        </table>
    </div>

    <h3><a href="#">Brief & Assets</a></h3>
    <div>
        <table id="DownloadFileData" class="data">
            <thead>
                <tr>
                    <th>Filetype</th>
                    <th>Description</th>
                    <th>Download Uri</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                {foreach $oObject->getSourceDownloadFiles() as $oDlFiles}
                    <tr>
                        <td>{$oDlFiles->getFiletype()}</td>
                        <td>{$oDlFiles->getDescription()}</td>
                        <td>{foreach $oDlFiles->getSourceSet() as $oSource}
                            {if !($oDlFiles->isExtenalLink())}
                                <a target="_blank" href="
                                   {if $oSource->isOpen()}
                                       https://mofilm.com/brief/{$oSource->getDownloadHash()}
                                   {else}
                                       http://admin.mofilm.com/download/generalDownloads?url={$oDlFiles->getFileLocation()}
                                   {/if}">
                                    Link
                                </a>
                            {else}
                                <a target="_blank" href="{$oDlFiles->getFilename()}">Link</a>
                            {/if}
                        {/foreach}
                    </td>
                    <td>
                        {if !($oDlFiles->isExtenalLink())}
                            <input type="file" id="FileUpload{$oDlFiles->getID()}" name="Files" class="small" />
                        {/if}
                    </td>
                </tr>
                {/foreach}
                </tbody>
            </table>
            <div class="DownloadFileDataControls hidden" style="padding-top: 10px; padding-bottom: 10px;">
                <div class="floatRight">
                    <div class="addAsset formIcon ui-state-default floatLeft" title="{t}Add Asset{/t}">Add Asset</div>
                </div>
                <div class="floatRight" style="padding-right: 10px;">
                    <div class="addBrief formIcon ui-state-default floatLeft" title="{t}Add Brief{/t}">Add Brief</div>
                </div>
                <div class="floatRight" style="padding-right: 10px;">
                    <div class="addNda formIcon ui-state-default floatLeft" title="{t}Add NDA{/t}">Add NDA</div>
                </div>
                <div class="clearBoth"></div>
            </div>
        </div>
       
         {if $oController->getAction() == 'editObject'}
        <h3><a href="#" id="ProjectBudget">Budget</a></h3>
        <div>
            The overall budget available for filmmaker / contributor compensation. <br>
            <table >
                <tbody> 
                    <tr>
                       <td> Total budget </td>
                       <td> 
                            {if !($BudgetData)}
                                <input type="text" id="EditVal" value="0" class="small" name = "BudgetOther" />
                            {else}  
                                <input type="text" id="EditVal" value="{$BudgetData->getOther()}" class="small" name = "BudgetOther" />
                            {/if}
                            <input type="hidden" name="ApprovedAmount" id="approvedAmt" value="{$approvedAmt}" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
            {/if}
        
        <h3><a href="#" >Submission Form</a></h3>
        <div>
            <table >
            <tbody>
                <tr >
                    <th>{t}
                        {if $NoData == 1}
                        <input type='checkbox' name='CustomGrant'  checked  value="N">   
                        {else}
                        <input type='checkbox' name='CustomGrant'    value="N">
                        {/if}
                         {/t}</th>
                    <th>
                         Standard Pro Idea Submission
                    </th>
                </tr>
                <tr >
                    <th>{t}
                        {if $NoData == 1}
                            <input type='checkbox' name='CustomGrant'  value="Y"> 
                         {else}
                            <input type='checkbox' name='CustomGrant' checked value="Y">  
                         {/if}   
                            
                        {/t}</th>
                    <th>
                         Custom single-question form
                    </th>
                </tr>
                <tr >
                    <th></th>
                    <td>
                        Enter the prompt for the question here. <br>
                        {if $NoData == 1}
                             <textarea cols="60" rows="5" name="CustomGrantQuestion"></textarea>
                         {else}
                              <textarea cols="60" rows="5" name="CustomGrantQuestion">{if $ParamData} {$ParamData->getParamValue()} {/if}</textarea>
                             {/if}
                        
                    </td>
                </tr>
           </tbody>
        </table>
            
        </div>
        <h3><a href="#">Status <span style="color:orange;float:right"> Published Pro projects appear on MOFILM.com</span></a></h3>
        <div>
            <table class="data">
                <tbody>

                    <tr>
                        <th>{t}Status{/t}</th>
                        <td colspan="4">{brandStatusSelect name='Status' selected=$oObject->getSourceStatus()}</td>
                    </tr>
                    {*<tr>
                    <th class="valignTop">
                    {t}Upload Logo{/t}<br />
                    <img src="{$adminSourceFolder}/{$oObject->getLogoName()}.jpg" width="50" height="28" alt="{$oObject->getName()}" title="{$oObject->getName()}" class="valignMiddle" style="border: 1px solid #000;" />
                    </th>
                    <td>
                    <input type="file" name="Logo" class="string" /><br />
                    {t}Please note:{/t}<br />
                    <em>
                    {t}Logos will be uploaded using the source name without any punctuation or special characters.{/t}<br />
                    {t}Logos will be resized to 261x139 (client) and 50x28 (admin) in JPEG format.{/t}
                    </em>
                    </td>
                    </tr>*}
                </tbody>
            </table>
        </div>

    </div>

    <script type="text/javascript">

            var availableSuppliers = {$availableSuppliers};


    </script>

    {elseif $oController->getAction() == 'deleteObject'}
        <p>{t}Are you sure you want to delete record named &quot;{$oObject->getName()}&quot;?{/t}</p>
        {/if}