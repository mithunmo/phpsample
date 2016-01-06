/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery(document).ready(function() {
    $('.broadCastApproveddate').on('change', function() {
        $('#broadcastDataChanged').val('Changed');
    }); // take care of select tags

    $('.broadCastNote').on('change', function() {
        $('#broadcastDataChanged').val('Changed');
    });
    $('.broadCastdate').on('change', function() {
        $('#broadcastDataChanged').val('Changed');
    });
    
    $('.broadCastCountryName').click( function() {
        $('#broadcastDataChanged').val('Changed');
    });

    if ($('.broadCastCountries').length > 0) {
        var cnt = $('.broadCastCountries div.broadCastCountryInfo').length;
        $('div.addBroadCastCountry').click(function() {
            
            cnt++;
            var newEles = $(
                    '<div class="broadCastCountryInfo line-bottom" id="CountryBroadcast' + cnt + '">'
                    + '<div class="formFieldContainer floatLeft">'
                    + '<h4>&nbsp;Country *</h4><p><select class="broadCastCountryName" onclick="getCountryID(this.value, ' + cnt + ')" name="Broadcast[' + cnt + '][CountryID]" id="myselect' + cnt + '"><option value="0" class="broadCastCountryName option">Not selected</option></select></div>'
                    + '<div class="formFieldContainer floatLeft">'
                    + '<h4>&nbsp;Broadcast Date *</h4>&nbsp;<input type="text" name="Broadcast[' + cnt + '][date]" onchange="checkDates(this.value)" class="datepicker broadCastdate" value="" title="Schedule date in year-month-day format e.g. Jan 1st 2010 is 2010-01-01" /></div>'
                    + '<div class="removeCurBroadcast formIcon ui-state-default floatLeft" title="Remove this broadcast" id="'+cnt+'"><span class="ui-icon ui-icon-minusthick"></span></div>'
                    +'<div class="clearBoth"></div>'
                    + '<hr></div></div>'
                    );

            newEles.appendTo('.broadCastCountries');
            $.each(countryList, function(index, value) {
                $.each(value, function(index1, value1) {
                    $('#myselect' + cnt).append(
                        $('<option class="broadCastCountryName option"></option>').val(index1).html(value1)
                    );
                });
           });
           $('.datepicker').datepicker({
                //minDate: '0', 
                changeMonth: true,
                changeYear: true,
                dateFormat: 'yy-mm-dd',
                yearRange: '2005:+2'
        });

           newEles.find('div.removeCurBroadcast').click(function() {
               removeID = $(this).attr('id');
                alert('CountryBroadcast'+removeID);
                    $(this).parents('#CountryBroadcast'+removeID).remove();
            });
        });
        
     $('div.removeCurBroadcast').click(function() {
                removeID = $(this).attr('id');
                $(this).parents('#CountryBroadcast'+removeID).remove();
                $('#CountryBroadcast'+removeID).text(function(index) {
                        return index + 1;
                });
              //  formChangedWarningBox();
        });   

        
    }
});
function getCountryID (id, rowIndex) {
    var ids = [];
    $('input.broadCastdate').each(function(index) {
            if ($(this).parent().parent().children().children('p').children('select').val() != 0) {
                ids.push($(this).parent().parent().children().children('p').children('select').val());
            }
        });
        orgLength = ids.length;
        uniLength = jQuery.unique( ids ).length;
       var ids = [];
        $('#myselect'+rowIndex).click(function(index1) {
            if(orgLength > uniLength) {
                message = mofilm.lang.messages.broadCastDuplicate;
                $('#body div.container').append('<div id="formErrorBox" class="messageBox error"><p>' + message + '</p></div>');
                $('#body div.container div.messageBox').delay(8500).slideUp(200);
                index1.preventDefault();
                return false;
            }
        });
        
}

function checkDates(countryBDate) {
    if ($('.broadCastApproveddate').val() > countryBDate && countryBDate != '') {
        message = mofilm.lang.messages.broadCastDateConflict;
        $('#body div.container').append('<div id="formErrorBox" class="messageBox error"><p>' + message + '</p></div>');
        $('#body div.container div.messageBox').delay(8500).slideUp(200);
        e.preventDefault();
        return false;
    }
}