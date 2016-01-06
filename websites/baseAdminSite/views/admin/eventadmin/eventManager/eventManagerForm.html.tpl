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
        var length = FMID.length;
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
                    //var index = Cont_name.substr(Cont_name.length - 3);
                    var index = Cont_name.replace("Contributors","");
                    var name = "FM"+index;
                    $('input[name="'+name+'"]').val(ui.item.key);
                    
                    },
                change: function (event, ui) {
                    if (!ui.item) { 
                        $(this).val("");
                        $('#body div.container').append('<div id="formErrorBox" class="messageBox error"><p> test error message</p></div>');
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
    <div class="hidden">
        <input type="hidden" name="PrimaryKey" value="{$oController->getPrimaryKey()|default:0}" />
        <input type="hidden" name="EventDataSetID" value="{$oObject->getEventDataSet()->getID()|default:0}" />
        <input type="hidden" name="Hash" value="{$oObject->getEventDataSet()->getHash()|default:0}" />
    </div>
    <div class="content">	
        <div class="daoAction">
            {if $oObject->getEventDataSet()->getHash()}
                <a href="http://www.mofilm.com/competitions/previewEvent/{$oObject->getEventDataSet()->getHash()}" title="{t}Preview{/t}" target="_blank">
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
        <h3><a href="#">Create a new Project</a></h3>
        <div>
            <table class="data">
                <tbody>
                    <tr>
                        <th>{t}Product{/t}</th>
                        <td colspan="4">      
                            {productDistinctSelect id="productListVideo" name='ProductID' selected=$oObject->getProductID() class="valignMiddle " }
                        </td>    
                    </tr>
                    
                    <tr>
                        <th>{t}Project Name{/t}</th>
                        <td colspan="4"><input type="text" name="Name" value="{$oObject->getName()}" class="string" /></td>
                    </tr>
                </tbody>     
            </table>

        </div>
        <h3><a href="#">Project Dates</a> </h3>
        <div>
            <table class="data">
                <tbody>
                    <tr>
                        <th>{t}Submission Start{/t}</th>
                        <td>
                            <input type="text" name="Startdate" value="{$oObject->getStartDate()|date_format:'%Y-%m-%d'}" class="date datepicker" />
                            <strong>@</strong>
                            {html_select_time field_array='StartdateTime' prefix='' display_seconds=false minute_interval=10 time=$oObject->getStartDate()}
                        </td>
                    </tr>
                    <tr>
                        <th>{t}Submission End{/t}</th>
                        <td colspan="4">
                            <input type="text" name="Enddate" value="{$oObject->getEndDate()|date_format:'%Y-%m-%d'}" class="date datepicker" />
                            <strong>@</strong>
                            {html_select_time field_array='EnddateTime' prefix='' display_seconds=false minute_interval=10 time=$oObject->getEndDate()}
                        </td>
                    </tr>
                    </tr>
                    
                </tbody></table></div> 
                    

        <h3 id="projectdetails" ><a href="#">Award Dates</a> </h3>
        <div>
            <table class="data">
                <tbody>
                    <tr>
                        <th>{t}Award Start{/t}</th>
                        <td>
                            <input type="text" name="AwardStartdate" value="{$oObject->getAwardStartDate()|date_format:'%Y-%m-%d'}" class="date datepicker" />
                            <strong>@</strong>
                            {html_select_time field_array='AwardStartdateTime' prefix='' display_seconds=false minute_interval=10 time=$oObject->getAwardStartDate()}
                        </td>
                        {*<th>{t}End Date{/t}</th>
                        <td>
                        <input type="text" name="AwardEnddate" value="{$oObject->getAwardEndDate()|date_format:'%Y-%m-%d'}" class="date datepicker" />
                        <strong>@</strong>
                        {html_select_time field_array='AwardEnddateTime' prefix='' display_seconds=false minute_interval=10 time=$oObject->getAwardEndDate()}
                        </td>*}
                    </tr>
                    <tr>
                        <th>{t}Award End{/t}</th>
                        <td colspan="4">
                            <input type="text" name="AwardEnddate" value="{$oObject->getAwardEndDate()|date_format:'%Y-%m-%d'}" class="date datepicker" />
                            <strong>@</strong>
                            {html_select_time field_array='AwardEnddateTime' prefix='' display_seconds=false minute_interval=10 time=$oObject->getAwardEndDate()}
                        </td>
                    </tr>
                    
                </tbody></table></div> 
        
        
        <h3 id="projectlandingdetails" ><a href="#">Event Landing Page</a> </h3>
        <div>
            <table class="data">
                <tbody>
                    <!--
                    <tr>
                        <th>{t}Name{/t}</th>
                        <td colspan="4"><input type="text" name="Name" value="{$oObject->getName()}" class="string" /></td>
                    </tr>
                    -->
                    <tr>
                        <th>{t}Invite Only{/t}</th>
                        <td>{yesNoSelect name='Hidden' selected=$oObject->getHidden()}</td>
                    </tr>
                    <tr>
                        <th>{t}Custom Design{/t}</th>
                        <td>{yesNoSelect name='Custom' selected=$oObject->getCustom()}</td>
                    </tr>
                    {*<tr>
                    <th>{t}Web Path{/t}</th>
                    <td colspan="4"><input type="text" name="Webpath" value="{$oObject->getWebpath()}" class="string" /></td>
                    </tr>*}

                    <tr>
                        <th>{t}Title{/t}</th>
                        <td colspan="4"><input type="text" id="DisplayName-project" name="DisplayName" value="{$oObject->getEventDataSet()->getName()|escape:'htmlall':'UTF-8'}" class="string" /></td>
                    </tr>
                    <tr>
                        <td colspan="5"><br />
                            <b>{t}Sitecopy{/t}</b><br /><br />
                            <textarea name="Sitecopy" rows="20" cols="40" class="tinymce">{$oObject->getEventDataSet()->getDescription()|escape:'htmlall':'UTF-8'}</textarea>
                            <br /></td>
                    </tr>
                    <tr>
                        <th>{t}Terms{/t}</th>
                        <td colspan="4">{termsSelect name='TermsID' selected=$oObject->getTermsID()}</td>
                    </tr>
                    <tr>
                        <th>{t}Instructions{/t}</th>
                        <td colspan="4">
                            <textarea name="Instructions" rows="5" cols="60">{$oObject->getInstructions()|escape:'htmlall':'UTF-8'}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>{t}Show Brand TBA icons{/t}</th>
                        <td>{yesNoSelect name='Tba' selected=$oObject->getTba()} Maximum 6 TBA icons will be shown.</td>
                    </tr>
                    

                    <tr>
                        <!--
                        <th>{t}Product{/t}</th>
                        <td colspan="4">      
                        {*productDistinctSelect id="productListVideo" name='ProductID' selected=$oObject->getProductID() class="valignMiddle " *}
                    </td>    
                        -->
                    </tr>

                </tbody>
            </table>
        </div>
                    
        {if $oController->getAction() == 'editObject' && $oModel->getProductID() == 7}
            <h3><a href="#">Design Images</a></h3>
            <div>
                <table class="data">
                    <tbody>
                        <tr>
                            <th class="valignTop">
                                {t}Upload Logo{/t}<br />
                                <img src="{$clientEventFolder}/logo/{$oObject->getLogoName()}.gif" width="50" height="28" alt="{$oObject->getName()}" title="{$oObject->getName()}" class="valignMiddle" style="border: 1px solid #000;" />
                            </th>
                            <td colspan="4">
                                <input type="file" id="EventLogo" name="EventLogo" class="string" /><br />
                                {t}Please note:{/t}<br />
                                <em>
                                    {t}Upload Logo of size and in Gif Format {/t}
                                </em>
                            </td>
                        </tr>
                        <tr>
                            <th class="valignTop">
                                {t}Upload Banner{/t}<br />
                            </th>
                            <td colspan="4">
                                {*<img src="{$clientEventFolder}/banner/{$oObject->getLogoName()}.png" width="50" height="28" alt="{$oObject->getName()}" title="{$oObject->getName()}" class="valignMiddle" style="border: 1px solid #000;" />*}
                                <br /><br />
                                <input type="file" id="EventBanner" name="EventBanner" class="string" /><br />
                                {t}Please note:{/t}<br />
                                <em>
                                    {t}Upload Banner of size {/t}
                                </em>
                            </td>
                        </tr>
                        <tr>
                            <th class="valignTop">
                                {t}Upload Filler{/t}<br />
                            </th>
                            <td colspan="4">
                                {*<img src="{$clientEventFolder}/filler/{$oObject->getLogoName()}.jpeg" width="50" height="28" alt="{$oObject->getName()}" title="{$oObject->getName()}" class="valignMiddle" style="border: 1px solid #000;" />*}
                                <br /><br />
                                <input type="file" id="EventFiller" name="EventFiller" class="string" /><br />
                                {t}Please note:{/t}<br />
                                <em>
                                    {t}Upload Filler of size {/t}
                                </em>
                            </td>
                        </tr>
                        <tr>
                            <th class="valignTop">
                                {t}Background Color{/t}<br />
                            </th>
                            <td colspan="4">
                                <br />
                                <input type="text" name="EventBgcolor" value="{$oObject->getBgcolor()}" class="small string" />
                                <br />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        {/if}
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
             All User
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
                         
        <h3 id="projectstatus" ><a href="#">Status </a> </h3>
        <div>
            <table class="data">
                <tbody>  <tr>
                        <th>{t}Status{/t}</th>
                        <td colspan="4">{eventStatusSelect name='Status' selected=$oObject->getStatus()}</td>
                    </tr>
                </tbody>
            </table>
        </div>   
        
        
    </div>
{elseif $oController->getAction() == 'deleteObject'}
    <p>{t}Are you sure you want to delete record named &quot;{$oObject->getName()}&quot;?{/t}</p>
{/if}
<script>
     var usersPermission = new Array();
        
</script>