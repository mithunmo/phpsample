/**
 * Mofilm Admin JS Resource
 *
 * @author Dave Redfern
 * @copyright Mofilm Ltd
 * @package mofilm
 * @subpackage websites_baseAdminSite_libraries
 * @version $Rev: 327 $
 */
jQuery(document).ready(function() {

    var messageBox = function(status, message) {
        $('#body div.container').append('<div class="messageBox ' + status + '"><p>' + message + '</p></div>');
        $('#body div.container div.messageBox').delay(2000).slideUp(200);
    };

    var formChangedWarningBox = function() {
        if ($('#formWarningBox').length == 0) {
            $('#body div.container').append('<div id="formWarningBox" class="messageBox warning"><p>' + mofilm.lang.messages.formContentsChanged + '</p></div>');
            $('#body div.container div.messageBox').delay(8500).slideUp(200);
        }
    };

    /*
     * Check for forms and attach a change() event listener to display a
     * warning that the form needs saving
     */
    if ($('form.monitor').length > 0) {
        $('form.monitor').each(function() {
            $(this).data('initialValues', $(this).serialize());
            $(this).change(function() {
                formChangedWarningBox();
            });
        });
    }

    /*
     * Make message boxes closeable
     */
    if ($('.messageBox').length > 0) {
        $('.messageBox.closeable').each(function() {
            $(this).append('<div class="click_to_close" style="cursor: pointer;"></div>');
        });
        $('.click_to_close').click(function() {
            $(this).parent().clearQueue();
            $(this).parent().slideUp(200);
        });
        $('div.messageBox').delay(5000).slideUp(200);
    }

    /*
     * Make MOTD ajaxy
     */
    if ($('.markAsRead').length > 0) {
        $('.markAsRead').click(function() {
            $.post(
                    $(this).attr('href'),
                    null,
                    function(data, textStatus, XMLHttpRequest) {
                        messageBox(data.status, data.message);
                        if (data.status == 'success') {
                            $('.motd').slideUp(200);
                        }
                    },
                    'json'
                    );
            return false;
        });
    }

    /*
     * Add a generic AJAX get call, expects back a JSON object containing status and message
     */
    if ($('a.ajaxUpdate').length > 0) {
        $('a.ajaxUpdate').click(function() {
            $.get(
                    $(this).attr('href') + "/as.json",
                    null,
                    function(data, textStatus, XMLHttpRequest) {
                        messageBox(data.status, data.message);
                        return false;
                    },
                    'json'
                    );
            return false;
        });
    }

    /*
     * Add rejection ajax updates
     */
    if ($('a.ajaxRejectUpdate').length > 0) {
        $('a.ajaxRejectUpdate').click(function() {
            oEle = $(this);
            $.get(
                    oEle.attr('href') + "/as.json",
                    null,
                    function(data, textStatus, XMLHttpRequest) {
                        messageBox(data.status, data.message);

                        oEle.parents('dd').prev().find('a').removeClass('status statusPending statusApproved');
                        oEle.parents('dd').prev().find('a').addClass('statusRejected');
                        return false;
                    },
                    'json'
                    );
            return false;
        });
    }

    /*
     * Add datepicker
     */
    if ($('.datepicker').length > 0) {
        $('.datepicker').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            yearRange: '2005:+2'
        });
    }

    /*
     * Add validation to profile
     */
    if ($('#profileForm').length > 0) {
        $('#profileForm').validate({
            rules: {
                Firstname: "required",
                Surname: "required",
                Password: {
                    minlength: 8,
                    equalTo: "#confirmPassword"
                },
                confirmPassword: {
                    equalTo: "#newPassword"
                }
            },
            messages: {
                Firstname: {
                    required: mofilm.lang.messages.profileFirstnameRequired
                },
                Surname: {
                    required: mofilm.lang.messages.profileSurnameRequired
                },
                Password: {
                    equalTo: mofilm.lang.messages.profilePasswordConfirm,
                    minlength: jQuery.format(mofilm.lang.messages.profilePasswordMinLength)
                },
                confirmPassword: {
                    equalTo: mofilm.lang.messages.profilePasswordConfirm
                }
            },
            submitHandler: function(form) {
                $.post(
                        "/account/profileUpdate/as.json",
                        $(form).serialize(),
                        function(data, textStatus, XMLHttpRequest) {
                            messageBox(data.status, data.message);
                        },
                        "json"
                        );
                return false;
            }
        });
    }

    /*
     * Add validation to login form
     */
    if ($('#loginForm').length > 0) {
        $('#loginForm').validate({
            rules: {
                username: "required",
                password: "required"
            },
            messages: {
                username: {
                    required: mofilm.lang.messages.loginErrorUsername
                },
                password: {
                    required: mofilm.lang.messages.loginErrorPassword
                }
            }
        });
    }

    /*
     * Add date selector to DOB field
     */
    if ($('#propertyDateOfBirth').length > 0) {
        $('#propertyDateOfBirth').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            yearRange: '1900:+0'
        });
    }

    /*
     * Use accordion on user info form
     */
    if ($('#userFormAccordion').length > 0) {
        $('#userFormAccordion').accordion({
            collapsible: true,
            autoHeight: false,
            navigation: true
        });
    }

    /*
     * Add fancy track editing controls
     */
    if ($('#trackData').length > 0) {
        var acOptions = {
            source: availableSuppliers,
            minLength: 1
        };
        var cnt = $('#trackData tbody tr').length;

        $('input.trackSupplier').autocomplete(acOptions);
        $('div.controls').show();

        $('div.addTrack').click(function() {
            cnt++;
            var newEles = $(
                    '<tr>' +
                    '<td><input type="hidden" name="Tracks[' + cnt + '][ID]" value="0" /><span class="recordNumber">' + ($('#trackData tbody tr').length + 1) + '</span></td>' +
                    '<td><input type="text" class="trackArtist" name="Tracks[' + cnt + '][Artist]" value="" /></td>' +
                    '<td><input type="text" class="trackTitle" name="Tracks[' + cnt + '][Title]" value="" /></td>' +
                    '<td><input type="text" class="trackSupplier" name="Tracks[' + cnt + '][Supplier]" value="" /></td>' +
                    '<td><div class="removeCurTrack formIcon ui-state-default floatLeft" title="' + mofilm.lang.messages.removeThisTrack + '"><span class="ui-icon ui-icon-minusthick"></span></div></td>' +
                    '</tr>'
                    );

            newEles.appendTo('#trackData tbody');
            newEles.find('.trackSupplier').autocomplete(acOptions);
            newEles.find('div.removeCurTrack').click(function() {
                $(this).parents('tr').remove();
                $('#trackData span.recordNumber').text(function(index) {
                    return index + 1;
                });
            });
        });

        $('input[type=checkbox].addRemoveControl').replaceWith(
                '<div class="removeCurTrack formIcon ui-state-default floatLeft" title="' + mofilm.lang.messages.removeThisTrack + '"><span class="ui-icon ui-icon-minusthick"></span></div>'
                );

        $('div.removeTrack').click(function() {
            $('#trackData tbody tr').last().remove();
        });

        $('div.removeCurTrack').click(function() {
            $(this).parents('tr').remove();
            $('#trackData span.recordNumber').text(function(index) {
                return index + 1;
            });
            formChangedWarningBox();
        });
    }

    /*
     * Add fancy prize editing controls
     */
    if ($('#prizeData').length > 0) {

        var cntp = $('#prizeData tbody tr').length;

        $('div.prizeControls').show();

        $('div.addPrize').click(function() {
            cntp++;
            var newEles = $(
                    '<tr>' +
                    '<td><input type="hidden" name="Prize[' + cntp + '][ID]" value="0" /><input type="text" class="prizePosition small" name="Prize[' + cntp + '][Position]" value="" /></td>' +
                    '<td><input type="text" class="prizeAmount small" name="Prize[' + cntp + '][Amount]" value="" /></td>' +
                    '<td><textarea cols="40" rows="1" name="Prize[' + cntp + '][Description]" class="prizeDescription"></textarea></td>' +
                    '<td><div id="new" class="removeCurPrize formIcon ui-state-default floatLeft" title="' + mofilm.lang.messages.removeThisPrize + '" ><span class="ui-icon ui-icon-minusthick"></span></div></td>' +
                    '</tr>'
                    );

            newEles.appendTo('#prizeData tbody');
            newEles.find('div.removeCurPrize').click(function() {
                $(this).parents('tr').remove();
                $('#prizeData span.recordNumber').text(function(index) {
                    return index + 1;
                });
            });
        });

//		$('input[type=checkbox].addRemovePrizeControl').replaceWith(
//			'<div class="removeCurPrize formIcon ui-state-default floatLeft" title="' + mofilm.lang.messages.removeThisPrize + '"><span class="ui-icon ui-icon-minusthick"></span></div>'
//		);

        $('div.removePrize').click(function() {
            $('#prizeData tbody tr').last().remove();
        });

        $('div.removeCurPrize').click(function() {
            if ($(this).attr('id') == 'new') {
                $(this).parents('tr').remove();
                $('#prizeData span.recordNumber').text(function(index) {
                    return index + 1;
                });
            } else {
                PrizeID = parseInt($(this).attr('id'));
                $.post(
                        '/admin/eventadmin/sourceManager/doAjaxDeletePrize/as.json',
                        {
                            SourcePrizeID: PrizeID
                        },
                function(data, textStatus, XMLHttpRequest) {
                    messageBox(data.status, data.message);
                    if (data.status == 'success') {
                        $('#prizeData #' + PrizeID).parents('tr').remove();
                    }
                },
                        'json'
                        );
            }
        });
    }

    /*
     * Add fancy prize editing controls
     */
    if ($('#DownloadFileData').length > 0) {

        var cntp = $('#DownloadFileData tbody tr').length;

        $('div.DownloadFileDataControls').show();

        $('div.addAsset').click(function() {
            cntp++;
            var newEles = $(
                    '<tr>' +
                    '<td><input type="hidden" name="DownloadFileData[' + cntp + '][ID]" value="0" />\n\
					     <input type="hidden" name="DownloadFileData[' + cntp + '][FileType]" value="assets" />\n\
					assets</td>' +
                    '<td><input type="text" class="DonwloadFileAssetDescription large" name="DownloadFileData[' + cntp + '][Description]" value="" /></td>' +
                    '<td><input type="text" class="DonwloadFileAssetName large" name="DownloadFileData[' + cntp + '][Name]" value="" /></td>' +
                    /*'<td><textarea cols="20" rows="1" name="DownloadFileData[' + cntp + '][Description]" class="DownloadFileDataDescription"></textarea></td>' +*/
                    '<td><div id="new" class="removeCurDownloadFileData formIcon ui-state-default floatLeft" title="' + mofilm.lang.messages.removeThisDownloadFileData + '" ><span class="ui-icon ui-icon-minusthick"></span></div></td>' +
                    '</tr>'
                    );

            newEles.appendTo('#DownloadFileData tbody');
            newEles.find('div.removeCurDownloadFileData').click(function() {
                $(this).parents('tr').remove();
                $('#DownloadFileData span.recordNumber').text(function(index) {
                    return index + 1;
                });
            });
        });

        $('div.addBrief').click(function() {
            cntp++;
            var newEles = $(
                    '<tr>' +
                    '<td><input type="hidden" name="DownloadFileData[' + cntp + '][ID]" value="0" />\n\
					     <input type="hidden" name="DownloadFileData[' + cntp + '][FileType]" value="brief" />\n\
					brief</td>' +
                    '<td><input type="text" class="DownloadFileBriefDescription large" name="DownloadFileData[' + cntp + '][Description]" value="" /></td>' +
                    '<td><input type="text" class="DonwloadFileBriefName large" name="DownloadFileData[' + cntp + '][Name]" value="" /></td>' +
                    /*'<td><textarea cols="40" rows="1" name="DownloadFileData[' + cntp + '][Description]" class="prizeDescription"></textarea></td>' +*/
                    '<td><div id="new" class="removeCurDownloadFileData formIcon ui-state-default floatLeft" title="' + mofilm.lang.messages.removeThisPrize + '" ><span class="ui-icon ui-icon-minusthick"></span></div></td>' +
                    '</tr>'
                    );

            newEles.appendTo('#DownloadFileData tbody');
            newEles.find('div.removeCurDownloadFileData').click(function() {
                $(this).parents('tr').remove();
                $('#DownloadFileData span.recordNumber').text(function(index) {
                    return index + 1;
                });
            });
        });

        $('div.addNda').click(function() {
            cntp++;
            var newEles = $(
                    '<tr>' +
                    '<td><input type="hidden" name="DownloadFileData[' + cntp + '][ID]" value="0" />\n\
					     <input type="hidden" name="DownloadFileData[' + cntp + '][FileType]" value="nda" />\n\
					nda</td>' +
                    '<td><input type="text" class="DonwloadFileNdaName large" name="DownloadFileData[' + cntp + '][Description]" value="" /></td>' +
                    '<td><input type="text" class="DonwloadFileNdaName large" name="DownloadFileData[' + cntp + '][Name]" value="" /></td>' +
                    /*'<td><textarea cols="40" rows="1" name="DownloadFileData[' + cntp + '][Description]" class="prizeDescription"></textarea></td>' +*/
                    '<td><div id="new" class="removeCurDownloadFileData formIcon ui-state-default floatLeft" title="' + mofilm.lang.messages.removeThisPrize + '" ><span class="ui-icon ui-icon-minusthick"></span></div></td>' +
                    '</tr>'
                    );

            newEles.appendTo('#DownloadFileData tbody');
            newEles.find('div.removeCurDownloadFileData').click(function() {
                $(this).parents('tr').remove();
                $('#DownloadFileData span.recordNumber').text(function(index) {
                    return index + 1;
                });
            });
        });

//		$('input[type=checkbox].addRemovePrizeControl').replaceWith(
//			'<div class="removeCurPrize formIcon ui-state-default floatLeft" title="' + mofilm.lang.messages.removeThisPrize + '"><span class="ui-icon ui-icon-minusthick"></span></div>'
//		);

        $('div.removePrize').click(function() {
            $('#prizeData tbody tr').last().remove();
        });

        $('div.removeCurPrize').click(function() {
            if ($(this).attr('id') == 'new') {
                $(this).parents('tr').remove();
                $('#prizeData span.recordNumber').text(function(index) {
                    return index + 1;
                });
            } else {
                PrizeID = parseInt($(this).attr('id'));
                $.post(
                        '/admin/eventadmin/sourceManager/doAjaxDeletePrize/as.json',
                        {
                            SourcePrizeID: PrizeID
                        },
                function(data, textStatus, XMLHttpRequest) {
                    messageBox(data.status, data.message);
                    if (data.status == 'success') {
                        $('#prizeData #' + PrizeID).parents('tr').remove();
                    }
                },
                        'json'
                        );
            }
        });
    }

    /*
     * Add icon hover effects
     */
    $('div.formIcon').hover(
            function() {
                $(this).addClass('ui-state-hover');
            },
            function() {
                $(this).removeClass('ui-state-hover');
            }
    );

    /*
     * Add shift-click multi-select to checkboxes
     */
    if ($('.shiftCheckEnable').length > 0) {
        $('.shiftCheckEnable').shiftcheckbox();
    }

    /*
     * Add control for event date ranges 
     */
    if ($('input.sourceManagerDate').length > 0) {
        $('input.sourceManagerDate').change(function() {
            today = new Date();
            obj = $(this);

            if (obj.is(':checked')) {
                obj.parent().find('.date').prop({
                    disabled: true,
                    value: ''
                });
            } else {
                day = parseInt(today.getDate());
                month = parseInt(today.getMonth()) + 1;
                if (month < 10) {
                    month = '0' + month;
                }
                if (day < 10) {
                    day = '0' + day;
                }

                obj.parent().find('.date').prop({
                    disabled: false,
                    value: today.getFullYear() + '-' + month + '-' + day
                });
            }
        });
    }

    /*
     * Add ajax calls for PMs
     */
    if ($('a.deletePm').length > 0) {
        $('a.deletePm').click(function(event) {
            uri = $(this).attr('href') + '/as.json';

            $.get(
                    uri,
                    function(data, textStatus, XMLHttpRequest) {
                        messageBox(data.status, data.message);
                    },
                    "json"
                    );

            $(this).parents('tr').remove();

            return false;
            eventListSources
        });
    }
    if ($('#pmRecipient').length > 0) {
        $('#pmRecipient').fcbkcomplete({
            json_url: '/account/pm/search/as.json',
            cache: false,
            filter_case: false,
            filter_hide: true,
            newel: true,
            maxshownitems: 30,
            maxitems: 1
        });
    }

    /*
     * Linked events / sources selection list
     */
    if ($('#eventList').length > 0 && $('#eventListSources').length > 0) {
        $('#eventList').change(function() {
            $.get(
                    '/admin/eventadmin/sourceManager/viewObjects/as.xml',
                    {
                        Offset: 0,
                        Search: 'Search',
                        EventID: $(this).val()

                    },
            function(data, textStatus) {
                var htmlOptions = '';
                $(data).find('source').each(function() {
                    if ($('#eventList').val() == 0) {
                        htmlOptions += '<option value="' + $(this).find('name').text() + '">' + $(this).find('name').text() + '</option>';
                    } else {
                        htmlOptions += '<option value="' + $(this).find('id').text() + '">' + $(this).find('name').text() + '</option>';
                    }
                });
                $('#eventListSources').html(htmlOptions);
            },
                    'xml'
                    );
        });
    }

    if ($('#eventListVideo').length > 0 && $('#eventListCorporates').length > 0) {
        $('#eventListVideo').change(function() {

            var brandSelectedID = $("#corporateListBrands :selected").text();
            var corpID = 0;
            corpID = $("#eventListCorporates :selected").val();
            if (corpID == 'Any Corporates') {
                corpID = 0;
            }
            if (brandSelectedID == 'Select Brands' || brandSelectedID == 'Any Brands') {
                $.get(
                        '/admin/eventadmin/brand/viewObjects/as.xml',
                        {
                            Offset: 0,
                            Search: 'Search',
                            EventID: $(this).val(),
                            CorporateID: corpID
                        },
                function(data, textStatus) {
                    var htmlOptions = '';
                    $(data).find('brand').each(function() {
                        htmlOptions += '<option value="' + $(this).find('id').text() + '">' + $(this).find('name').text() + '</option>';

                    });
                    $('#corporateListBrands').html(htmlOptions);
                },
                        'xml'
                        );
            }
        });
    }

    if ($('#corporateListBrands').length > 0 && $('#corporateListBrands').length > 0) {
        $('#corporateListBrands').change(function() {

            var corpID = 0;
            corpID = $("#eventListCorporates :selected").val();
            if (corpID == 'Any Corporates') {
                corpID = 0;
            }

            var brandArray = $('#corporateListBrands :selected').val().split('-');
            var brandID = brandArray[0];
            $.get(
                    '/admin/eventadmin/eventManager/viewObjects/as.xml',
                    {
                        Offset: 0,
                        Search: 'Search',
                        BrandID: brandID,
                        CorporateID: corpID
                    },
            function(data, textStatus) {
                var htmlOptions = '';
                $(data).find('event').each(function() {
                    htmlOptions += '<option value="' + $(this).find('id').text() + '">' + $(this).find('name').text() + '</option>';

                });
                $('#eventListVideo').html(htmlOptions);
            },
                    'xml'
                    );

        });
    }

    /*
     * Linked corporate / brand selection list
     */
    if ($('#eventListCorporates').length > 0 && $('#corporateListBrands').length > 0) {
        $('#eventListCorporates').change(function() {
            $.get(
                    '/admin/eventadmin/brand/viewObjects/as.xml',
                    {
                        Offset: 0,
                        Search: 'Search',
                        CorporateID: $(this).val(),
                        EventID: 0
                    },
            function(data, textStatus) {
                var htmlOptions = '';

                $(data).find('brand').each(function() {
                    htmlOptions += '<option value="' + $(this).find('id').text() + '">' + $(this).find('name').text() + '</option>';

                });
                $('#corporateListBrands').html(htmlOptions);
            },
                    'xml'
                    );
            $.get(
                    '/admin/eventadmin/eventManager/viewObjects/as.xml',
                    {
                        Offset: 0,
                        Search: 'Search',
                        CorporateID: $(this).val(),
                        BrandID: 0
                    },
            function(data, textStatus) {
                var htmlEventOptions = '';

                $(data).find('event').each(function() {
                    htmlEventOptions += '<option value="' + $(this).find('id').text() + '">' + $(this).find('name').text() + '</option>';

                });
                $('#eventListVideo').html(htmlEventOptions);
            },
                    'xml'
                    );
        });
    }

    $('#corporateListBrands').change(function() {

        var brandArray = $('#corporateListBrands').val().split('-');
        $('#brandID').val(brandArray[0]);
        if (brandArray.length <= 2) {
            $('#brandName').val(brandArray[1]);
        } else {
            brandArray.shift();
            $('#brandName').val(brandArray.join('-'));
        }
    });
    /*
     * Add report validation
     */
    if ($('#reportForm').length > 0) {
        $('#reportSubmit').click(function(event) {
            $.post('/reports/validate/as.json', $('#reportForm').serialize(), function(data, textStatus, XMLHttpRequest) {
                if (data.message == 'ok') {
                    $('#reportForm').submit();
                } else {
                    messageBox(data.status, data.message);
                    return false;
                }
            }, 'json'
                    );
            event.stopPropagation();
            return false;
        });
    }

    /*
     * Add collapse/expand to dashboard
     */
    if ($('div.event.collapsed').length > 0) {
        $('div.event.collapsed').each(function() {
            $(this).find('.eventIcon').hide();
            $(this).find('.stats').hide();
            $(this).prepend('<div class="ui-icon ui-corner-all ui-state-default ui-icon-plusthick"></div>');
            $(this).find('div.ui-icon').click(
                    function() {
                        $(this).parent().find('.eventIcon').toggle();
                        $(this).parent().find('.stats').toggle();
                        if ($(this).hasClass('ui-icon-plusthick')) {
                            $(this).removeClass('ui-icon-plusthick').addClass('ui-icon-minusthick');
                        } else {
                            $(this).removeClass('ui-icon-minusthick').addClass('ui-icon-plusthick');
                        }
                    }).hover(
                    function() {
                        $(this).addClass('ui-state-hover');
                    },
                    function() {
                        $(this).removeClass('ui-state-hover');
                    }
            );
        });
    }

    /**
     * Adds the event bookmark icon and events to the object
     *
     * @param domObject
     * @return void
     */
    var addBookmark = function(domObject) {
        domObject.find('.ui-icon-bookmark, .ui-icon-trash').remove();
        domObject.prepend('<div class="floatRight pointer ui-icon ui-corner-all ui-state-default ui-icon-bookmark" title="' + mofilm.lang.messages.dashboardBookmarkEvent + '"></div>');
        domObject.find('div.ui-icon.ui-icon-bookmark').click(
                function() {
                    var event = $(this).parents('div.event');
                    var eventId = event.attr('id').replace(/event_/, '');

                    $.post(
                            '/account/bookmarkEvent/as.json',
                            {
                                EventID: eventId
                            },
                    function(data, textStatus, XMLHttpRequest) {
                        messageBox(data.status, data.message);
                        if (data.status == 'success') {
                            $('h3#bookmarkedEvents').after(event);
                            addRemoveBookmark(event);
                        }

                        return false;
                    },
                            'json'
                            );

                    return false;
                }).hover(
                function() {
                    $(this).addClass('ui-state-hover');
                },
                function() {
                    $(this).removeClass('ui-state-hover');
                }
        );
    };

    /**
     * Adds the event remove bookmark icon and events
     *
     * @param domObject
     * @return void
     */
    var addRemoveBookmark = function(domObject) {
        domObject.find('.ui-icon-bookmark, .ui-icon-trash').remove();
        domObject.prepend('<div class="floatRight pointer ui-icon ui-corner-all ui-state-default ui-icon-trash" title="' + mofilm.lang.messages.dashboardBookmarkRemove + '"></div>');
        domObject.find('div.ui-icon.ui-icon-trash').click(
                function() {
                    var event = $(this).parents('div.event');
                    var eventId = event.attr('id').replace(/event_/, '');

                    $.post(
                            '/account/bookmarkRemove/as.json',
                            {
                                EventID: eventId
                            },
                    function(data, textStatus, XMLHttpRequest) {
                        messageBox(data.status, data.message);

                        if (data.status == 'success') {
                            $('h3#availableEvents').after(event);
                            addBookmark(event);
                        }

                        return false;
                    },
                            'json'
                            );

                    return false;
                }).hover(
                function() {
                    $(this).addClass('ui-state-hover');
                },
                function() {
                    $(this).removeClass('ui-state-hover');
                }
        );
    };

    /*
     * Add bookmarking to events on dashboard
     */
    if ($('div.event.bookmark').length > 0) {
        $('div.event.bookmark').each(function() {
            addBookmark($(this));
        });
    }

    /*
     * Add removing bookmarks to events on dashboard
     */
    if ($('div.event.removeBookmark').length > 0) {
        $('div.event.removeBookmark').each(function() {
            addRemoveBookmark($(this));
        });
    }

    /*
     * Add "add-to-favourite" ajax actions
     */
    if ($('.addToFavourites').length > 0) {
        $('a.addToFavourites').click(function(event) {
            var ele = $(this);
            $.get(
                    $(this).attr('href'),
                    null,
                    function(data, textStatus, XMLHttpRequest) {
                        messageBox(data.status, data.message);
                        if (data.status == 'success') {
                            ele.children('img').attr('src', '/themes/mofilm/images/icons/16x16/bookmark-marked.png');
                        }
                    },
                    "json"
                    );
            return false;
        });
    }

    /*
     * Remove "favourite" ajax actions
     */
    if ($('.removeFromFavourites').length > 0) {
        var imgSrc = '';
        $('a.removeFromFavourites').hover(
                function(event) {
                    imgSrc = $(this).children('img').attr('src');
                    $(this).children('img').attr('src', '/themes/mofilm/images/icons/16x16/bookmark-delete.png');
                },
                function(event) {
                    $(this).children('img').attr('src', imgSrc);
                }
        );
        $('a.removeFromFavourites').click(function(event) {
            var ele = $(this);
            $.get(
                    $(this).attr('href'),
                    null,
                    function(data, textStatus, XMLHttpRequest) {
                        messageBox(data.status, data.message);
                        if (data.status == 'success') {
                            imgSrc = '/themes/mofilm/images/icons/16x16/bookmark.png';
                            ele.children('img').attr('src', '/themes/mofilm/images/icons/16x16/bookmark.png');
                        }
                    },
                    "json"
                    );
            return false;
        });
    }

    /*
     * Add favourite tools
     */
    if ($('#favourites').length > 0) {
        $('input[type=checkbox].addRemoveControl').replaceWith(
                '<div class="removeCurFavourite formIcon ui-state-default floatLeft" title="' + mofilm.lang.messages.removeThisFavourite + '"><span class="ui-icon ui-icon-minusthick"></span></div>'
                );

        $('div.removeCurFavourite').click(function() {
            $(this).parents('tr').remove();
            $('#favourites span.recordNumber').text(function(index) {
                return index + 1;
            });
            formChangedWarningBox();
        });
    }

    /*
     * Add moderation comment
     */
    if ($('#moderationCommentForm').length > 0) {
        $('#moderationCommentForm').submit(function() {
            $.post(
                    $('#moderationCommentForm').attr('action'),
                    $('#moderationCommentForm').serialize(),
                    function(data, textStatus, XMLHttpRequest) {
                        messageBox(data.status, data.message);
                        $('#moderationCommentForm').remove();
                    },
                    'json'
                    );
            return false;
        });
    }

    /*
     * Add comment submitting
     */
    if ($('#movieCommentBox').length > 0) {
        $('#movieCommentPost').click(function() {
            $.post(
                    '/videos/doComment',
                    {
                        MovieID: $('#movieCommentMovieID').val(),
                        Comment: $('#movieComment').val()
                    },
            function(data, textStatus, XMLHttpRequest) {
                messageBox(data.status, data.message);
                $('#movieComment').val('');

                $.get(
                        '/videos/commentList',
                        {
                            MovieID: $('#MasterMovieID').val()
                        },
                function(data, textStatus, XMLHttpRequest) {
                    $('#movieCommentHistory').replaceWith(data);
                },
                        'html'
                        );
            },
                    'json'
                    );
            return false;
        });
    }

    /*
     * Add message template support
     */
    if ($('#MsgTemplateID').length > 0) {
        $('#MsgTemplateID').change(function(event) {
            $.get(
                    '/users/msgtmp/as.json',
                    {
                        TemplateID: $(this).val()
                    },
            function(data, textStatus, XMLHttpRequest) {
                if (data.message) {
                    $('#MessageBody').text(data.message);
                }
            },
                    "json"
                    );
            return false;
        });
    }

    /*
     * Add event source stats
     */
    if ($('.sourceStats').length > 0) {
        $('.sourceStats').addClass('link');
        $('.sourceStats').click(function() {
            var oEle = $(this);
            if (oEle.next().next('table.data').length < 1) {
                $.get(
                        '/admin/eventadmin/eventManager/sourceStats',
                        {
                            EventID: $(this).attr('id').replace(/event_/, '')
                        },
                function(data, textStatus, XMLHttpRequest) {
                    oEle.next().next('.sourceStatsResults').replaceWith(data);
                },
                        'html'
                        );
            } else {
                oEle.next().next('table.data').toggle();
            }
            return false;
        });
    }

    /*
     * Add event grant stats
     */
    if ($('.grantStats').length > 0) {
        $('.grantStats').addClass('link');
        $('.grantStats').click(function() {
            var oEle = $(this);

            if (oEle.next().next('table.data').length < 1) {
                $.get(
                        '/admin/eventadmin/eventManager/grantStats',
                        {
                            EventID: $(this).attr('id').replace(/event_/, '')
                        },
                function(data, textStatus, XMLHttpRequest) {
                    oEle.next().next('.grantStatsResults').replaceWith(data);
                },
                        'html'
                        );
            } else {
                oEle.next().next('table.data').toggle();
            }
            return false;
        });
    }


    $('#grantDocSubmitButton').click(function() {
        $('#grantsApprovalForm').submit();
    });

    if ($('#grantsApprovalForm').length > 0) {
        $('#userMovieGrantsSubmit').click(function() {
            var AllowedSubmit =1;
            if ($('#GrantedStatus').val() == 'Approved' && $('#GrantedAmount').val() <= 0) {
                alert('Granted Amount should be greater than 0.');
                return false;
            } else {
                if ($('#GrantedStatus').val() == 'Approved' && $('#GrantedAmount').val() > 0) {
                    var GrantsAvailable = 0;
                    var GrantsDispersed =0 ;
                    var BufferGrant = 0;
                    var buffer = 0;
                    var TotalGrants = 0;
                    var AllowedGrants = 0;
                    var GrantedAmount = 0;
                    var ExistingGrantAmount = 0;
                    var grantExtraAmnt = 0;

                    GrantsAvailable = $('#GrantsAvailable').val();
                    GrantsDispersed = $('#GrantDispersed').val();
                    BufferGrant = $('#bufferGrant').val();
                    buffer = (GrantsAvailable*BufferGrant)/100;
                    TotalGrants = buffer+Number(GrantsAvailable);
                    AllowedGrants = TotalGrants-GrantsDispersed;
                    GrantedAmount = $('#GrantedAmount').val();
                    ExistingGrantAmount = $('#existingGrantAmount').val();
                    if(Number(ExistingGrantAmount)!== Number(GrantedAmount)){
                    	if(Number(GrantedAmount) > Number(ExistingGrantAmount)){
                    		grantExtraAmnt = Number(GrantsDispersed) + (Number(GrantedAmount)-Number(ExistingGrantAmount));
                    	}
                    }
           
                    if(ExistingGrantAmount > GrantedAmount){
                        alert('In order to decrease an approved grant amount please use payments admin. You may need to request this from the finance team directly. Thank you!');
                        AllowedSubmit = 0;
                    }else{
                    	  if(grantExtraAmnt > TotalGrants){
                    		 alert('The grant pot and agreed over-granting buffer has been spent. Please speak with the finance team to agree a greater buffer.');
                             AllowedSubmit = 0;
                    	  }
                        /*if(GrantedAmount > AllowedGrants ){
                            alert('The grant pot and agreed over-granting buffer has been spent. Please speak with the finance team to agree a greater buffer.');
                            AllowedSubmit = 0;
                        }*/
                    }
                }
                if(AllowedSubmit == 1) {
                if (confirm('Are you sure you wish to make this change to the grant?')) {
                    $.post(
                            '/grants/doEdit/as.json',
                            {
                                GrantedAmount: $('#GrantedAmount').val(),
                                GrantedStatus: $('#GrantedStatus').val(),
                                ModeratorComments: $('#ModeratorComments').val(),
                                GrantID: $('#GrantID').val(),
                                MovieID: $('#MovieID').val(),
                                DocumentAgreement: $('#documentAgreement').is(':checked') ? '1' : '0',
                                DocumentBankDetails: $('#documentBankDetails').is(':checked') ? '1' : '0',
                                DocumentIdProof: $('#documentIdProof').is(':checked') ? '1' : '0',
                                DocumentReceipts: $('#documentReceipts').is(':checked') ? '1' : '0'
                            },
                    function(data, textStatus, XMLHttpRequest) {
                        //window.location = '/grants';
                        messageBox(data.status, data.message);
                    },
                            'json'
                            );
                } else {
                    return false;
                }
            }
        }
            return false;
        });
    }

    if ($('#grantsApprovalEmailCommunication').length > 0) {
        $('#EmailCommunicationSend').click(function() {
            $.post(
                    '/grants/doSendEmail/as.json',
                    {
                        FilmMakerID: $('#FilmMakerID').val(),
                        GrantID: $('#GrantID').val(),
                        EmailMessage: $('#EmailMessage').val()
                    },
            function(data, textStatus, XMLHttpRequest) {
                window.location = '/grants/edit/' + $('#GrantID').val();
                messageBox(data.status, data.message);
            },
                    'json'
                    );
            return false;
        });
    }

    $('#GrantedStatus').change(function() {
        $('#GrantedAmount').removeAttr("readonly");
        if ($('#GrantedStatus').val() !== 'Approved') {
            $('#GrantedAmount').val("");
            $('#GrantedAmount').attr("readonly", "readonly");
        }
    });

    /*
     * Add award controls
     */
    if ($('.addChangeAward').length > 0) {
        $('.addChangeAward span').addClass('link');
        $('.addChangeAward span').click(function() {
            $('.addChangeAwardForm .awardFormSave').button();
            $('.addChangeFormOptions input').customInput();
            $('.addChangeAwardForm').slideToggle();
        });

        //awardPositionHolder
        $('.addChangeFormOptions input[name=Award]').change(function() {
            if ($('.addChangeFormOptions input[name=Award]:checked').val() == 'Finalist') {
                $('#awardPositionHolder').show();
            } else {
                $('#awardPositionHolder').hide();
            }
        });

        $('.awardFormSave').click(function() {
            if (confirm('Are you sure you wish to make this change to the awards?')) {
                var bocAwardVal = '';
                if ($('.addChangeFormOptions input[name=BestOfClientAward]:checked')) {
                    bocAwardVal = $('.addChangeFormOptions input[name=BestOfClientAward]:checked').val()
                }
                ;

                $.post(
                        '/videos/doAwardUpdate/as.json',
                        {
                            MovieID: $('#MasterMovieID').val(),
                            Award: $('.addChangeFormOptions input[name=Award]:checked').val(),
                            bocAward: bocAwardVal,
                            Position: $('#awardPosition option:selected').val()
                        },
                function(data, textStatus, XMLHttpRequest) {
                    messageBox(data.status, data.message);

                    if ($('.addChangeFormOptions input[name=Award]:checked').val() == 'remove') {
                        $('dl .award').remove();
                    }

                    $.get(
                            '/videos/awardList',
                            {
                                MovieID: $('#MasterMovieID').val()
                            },
                    function(data, textStatus, XMLHttpRequest) {
                        $('#movieAwardsHistory').replaceWith(data);
                    },
                            'html'
                            );
                },
                        'json'
                        );
                $('.addChangeAwardForm').slideToggle();
            } else {
                $('.addChangeAwardForm').slideToggle();
            }

            return false;
        });
    }

    /*
     * Add fancy state editing controls
     */
    if ($('#stateData').length > 0) {
        var cnt = $('#stateData tbody tr').length;
        $('div.controls').show();

        $('div.addState').click(function() {
            cnt++;
            var newEles = $(
                    '<tr>' +
                    '<td><input type="hidden" name="States[' + cnt + '][ID]" value="0" /><span class="recordNumber">' + ($('#stateData tbody tr').length + 1) + '</span></td>' +
                    '<td><input type="text" class="stateName long" name="States[' + cnt + '][Name]" value="" /></td>' +
                    '<td><input type="text" class="stateAbbr short" name="States[' + cnt + '][Abbr]" value="" /></td>' +
                    '<td><div class="removeCurState formIcon ui-state-default floatLeft" title="' + mofilm.lang.messages.removeThisItem + '"><span class="ui-icon ui-icon-minusthick"></span></div></td>' +
                    '</tr>'
                    );

            newEles.appendTo('#stateData tbody');
            newEles.find('div.removeCurState').click(function() {
                $(this).parents('tr').remove();
                $('#stateData span.recordNumber').text(function(index) {
                    return index + 1;
                });
            });
        });

        $('input[type=checkbox].addRemoveControl').replaceWith(
                '<div class="removeCurState formIcon ui-state-default floatLeft" title="' + mofilm.lang.messages.removeThisItem + '"><span class="ui-icon ui-icon-minusthick"></span></div>'
                );

        $('div.removeState').click(function() {
            $('#stateData tbody tr').last().remove();
        });

        $('div.removeCurState').click(function() {
            $(this).parents('tr').remove();
            $('#stateData span.recordNumber').text(function(index) {
                return index + 1;
            });
            formChangedWarningBox();
        });
    }

    if ($('#sendNewsletterSublist').length > 0) {
        $('#sendNewsletterSublist').change(function() {
            $('#sendNewsletterEventlist').attr('disabled', 'disabled');
            $('#sendNewsletterEventParamslist').attr('disabled', 'disabled');

        });
    }

    if ($('#sendNewsletterEventlist').length > 0) {
        $('#sendNewsletterEventlist').change(function() {
            $('#sendNewsletterSublist').attr('disabled', 'disabled');
        });
    }

    if ($('.sendnl').length > 0) {

        $('.sendnl').click(function() {
            var sendlink = $(this).attr('href');
            $.get(
                    $(this).attr('href') + "/as.json",
                    null,
                    function(data) {
                        alert(data.name);
                    },
                    'json'
                    );
            return false;
        });
    }

    if ($('#pb1').length > 0) {
        $("#pb1").progressBar();
    }

    if ($('#trackStats').length > 0) {
        var val = $('#newslettertrack').val();
        count = 0;
        $.get(
                "/admin/commsCentre/newsLetterMofilm/track/nlview/as.json",
                {
                    Nlid: $('#newslettertrack').val()
                },
        function(data) {
            $("#pb1").progressBar(parseInt(data.count));
        },
                'json'
                );

        $.jqplot.config.enablePlugins = true;

        $.get(
                "/admin/commsCentre/newsLetterMofilm/track/nlplot/as.json",
                {Nlid: $('#newslettertrack').val()},
        function(data) {
            plot9 = $.jqplot('chartdiv', [data.name],
                    {
                        title: 'Newsletter View details',
                        series: [{lineWidth: 5, markerOptions: {style: 'square'}}],
                        axesDefaults: {
                            tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                            tickOptions: {
                                angle: 30
                            }
                        },
                        axes: {
                            xaxis: {
                                renderer: $.jqplot.CategoryAxisRenderer
                            },
                            yaxis: {
                                min: 0,
                                tickInterval: data.tickInterval
                            }
                        }
                    });
        },
                'json'
                );
    }


    if ($('#newsletterMessageText').length > 0) {
        $('#newsletterMessageText').focus(function() {
            var StrippedString = $('#newsletterMessageBody').val().replace(/(<([^>]+)>)/ig, "");
            $('#newsletterMessageText').val(StrippedString);
        });
    }

    if ($('#nlTemplate').length > 0) {
        $('#nlTemplate').change(function() {
            var nlTemplateId = $('#nlTemplate').val();
            $.get(
                    "/admin/commsCentre/newsLetterMofilm/newslettertemplate/getHtml/as.json",
                    {ID: $('#nlTemplate').val()},
            function(data) {
                $('#newsletterMessageBody').val(data.html);
            },
                    'json'
                    );


        });
    }

    if ($('#templateTimePicker').length > 0) {
        $('#templateTimePicker').datetimepicker({
            timeFormat: 'hh:mm:ss',
            dateFormat: 'yy-mm-dd'
        });
    }

    if ($('.tinymce').length > 0) {
        $('textarea.tinymce').tinymce({
            // Location of TinyMCE script
            script_url: '/libraries/jquery-plugins/tinymce/jscripts/tiny_mce/tiny_mce.js',
            // General options
            theme: "advanced",
            plugins: "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
            // Theme options
            theme_advanced_buttons1: "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
            theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime,|,forecolor,backcolor",
            theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl",
            theme_advanced_buttons4: "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,preview",
            theme_advanced_toolbar_location: "top",
            theme_advanced_toolbar_align: "left",
            theme_advanced_statusbar_location: "bottom",
            theme_advanced_resizing: true,
            plugin_preview_width: "700",
            plugin_preview_height: "400",
            file_browser_callback: "fileBrowser",
            relative_urls: false,
            content_css: "css/content.css"

        });
    }

    if ($('#appMessageDynamic').length > 0) {
        $('#appMessageDynamic').live('change', function() {
            $('#MessageBodyId').append($('#appMessageDynamic').val());
        });
    }

    if ($('.translate').length > 0) {
        $('.translate').click(function() {
            var sendlink = $(this).attr('href');
            var val = $('#appLanguage').val();
            var newlink = sendlink + "/" + val;
            $(this).attr('href', newlink);
            window.location = $(this).attr("href");
            return false;
        });
    }

    if ($('#appMessageGroupID').length > 0) {
        $('#appMessageGroupID').change(function() {
            $('#appMessageDynamic').empty();
            $.get(
                    "/admin/commsCentre/appMessages/dProperties/as.json",
                    {MessageGroupID: $('#appMessageGroupID').val()},
            function(data) {
                $('#appMessageDynamic').append('<option> Choose the properties </option>')
                $.each(data, function(index) {
                    $('#appMessageDynamic').append('<option value="' + data[index] + '">' + data[index] + '</option>');
                });
            },
                    'json'
                    );

        });
    }

    if ($('.single').length > 0) {
        $('.single').click(function() {

            var txt = '<form id="uploadFormData" name="formData" method="post" action="/help/helpPages/doUpload" enctype="multipart/form-data"><input type="file" name="helpImageUpload" value="" class="long" /><div class="jqibuttons"><input type="submit" value="Go" /></div></form>';
            $.prompt(txt, {buttons: {Cancel: false}});
        });
    }

    if ($('#userSearch').length > 0) {
        $('#userAddList').click(function() {
            $("#userSearch td:nth-child(3) input:checked").each(function() {
                var val = $(this).parent().prev().text();
                var id = $(this).parent().next().text();
                $.get(
                        "/admin/commsCentre/newsLetterMofilm/manageSubscription/userAdd/as.json",
                        {
                            UserEmail: val,
                            UserID: id,
                            ListID: $('#ListID').val()

                        },
                function(data) {
                },
                        'json'
                        );
            });
            alert("Task Completed");
            return false;
        });

        $('#userDeleteList').click(function() {
            $("#userSearch td:nth-child(3) input:checked").each(function() {
                var val = $(this).parent().prev().text();
                var id = $(this).parent().next().text();
                $.get(
                        "/admin/commsCentre/newsLetterMofilm/manageSubscription/userDelete/as.json",
                        {
                            UserEmail: val,
                            UserID: id,
                            ListID: $('#ListID').val()

                        },
                function(data) {
                },
                        'json'
                        );
            });
            alert("Task completed");
            return false;
        });

    }

    if ($('#userSelectAll').length > 0) {
        $('#userSelectAll').click(function() {
            $("input[type='checkbox']").attr('checked', true);
        });
    }

    if ($('#userUnSelectAll').length > 0) {
        $('#userUnSelectAll').click(function() {
            $("input[type='checkbox']").attr('checked', false);
        });
    }

    if ($('#sendNewsletterEventParamslist').length > 0) {
        $('#sendNewsletterEventParamslist').change(function() {
            if ($('#sendNewsletterEventParamslist').val() == 3) {
                $('#videoRatingID').removeAttr("disabled");
            } else {
                $('#videoRatingID').attr("disabled", "disabled");
            }
        });
    }

    if ($('#sendnewsletterwizard').length > 0) {
        $('#adminFormData').attr("id", "multipage");
        $("#multipage").formwizard({
            formPluginEnabled: true,
            validationEnabled: true,
            focusFirstInput: true,
            disableUIStyles: true
        }
        );
    }
    /*
     if ( $('#CCAForm').length > 0 ) {
     $('.save').click(function() {
     $("#adminFormData").validate({
     rules: {
     Cca: {
     required: true,
     accept: "pdf"
     },
     videoRating: {
     required: true,
     number: true
     }
     },
     messages: {
     Cca: {
     required: "Only PDF supported"
     },
     videoRating: {
     required: "Enter a valid rating"
     }
     
     }
     });
     });
     }
     */

    /*
     * Linked events / sources selection list for CCA and includes multiple selection
     */
    if ($('#eventListCCA').length > 0 && $('#eventListSourcesCCA').length > 0) {
        $('#eventListCCA').change(function() {
            $.get(
                    '/admin/eventadmin/sourceManager/viewObjects/as.xml',
                    {
                        Offset: 0,
                        Search: 'Search',
                        EventID: $(this).val()
                    },
            function(data, textStatus) {
                var htmlOptions = '';
                $(data).find('source').each(function() {
                    htmlOptions += '<option value="' + $(this).find('id').text() + '">' + $(this).find('name').text() + '</option>';
                });
                $("#eventListSourcesCCA").attr('size', parseInt($(data).find('resultCount').text()) + 1);
                $('#eventListSourcesCCA').html(htmlOptions);
            },
                    'xml'
                    );
        });
    }

    /**
     * Processes the license data from ajax request to musicLicense and displays them
     */
    function handleLicenseData(data) {
        $('#licenseContent').empty();
        $(data).find('data').each(function() {
            var licenseID = $(this).find('license').text();
            var trackName = $(this).find('trackName').text();
            var source = $(this).find('source').text();
            if ($(this).find('status').text() == 0) {
                $('#licenseContent').append("<tr><td>" + licenseID + "</td> <td>" + trackName + "</td> <td><img src='/themes/mofilm/images/icons/16x16/valid.png'></td><td>" + source + " </td></tr>");
            } else {
                $('#licenseContent').append("<tr><td>" + licenseID + "</td> <td>" + trackName + "</td> <td><img src='/themes/mofilm/images/icons/16x16/invalid.png'></td><td>" + source + " </td></tr>");
            }
        });

        $("#licenseContent").append($(data).find("count").text());
        $("#licenseContent").append($(data).find("error").text());
    }

    /**
     * Adds an extra text box for accepting licensingID and validates the licenseID from mofilmmusicAPI
     */
    if ($('#validateLicense').length > 0) {
        var i = 1;
        $('#addLicense').click(function() {
            $("<tr> <th>License ID </th> <td> <input type='text' id='LicenseID" + i + "'></td></tr>").insertBefore("#licenseText");
            i++;
        });

        $('#validateLicense').click(function() {
            $('#licenseContent').empty();
            var licenseArray = new Array();
            for (j = 0; j < i; j++) {
                licenseArray[j] = $('#LicenseID' + j).val();
                if (licenseArray[j].length <= 0 || $('#MasterMovieID').val().length <= 0 || licenseArray[j].match(/\d{1,}-\d{1,}-\d{1,4}/) == null) {
                    $('#licenseContent').empty();
                    $('#licenseContent').append("Invalid licenseID or movieID");
                    return false;
                }
            }

            var movieID = $('#MasterMovieID').val();
            $("#licenseContent").append('<img src="/themes/mofilm/images/loading.gif" alt="Wait" />');

            $.post(
                    "/admin/movieadmin/musicLicense/validate/as.xml",
                    {
                        LicenseID: licenseArray,
                        MovieID: movieID
                    },
            function(data) {
                handleLicenseData(data);
            },
                    'xml'
                    );

            return false;
        });
    }

    if ($("#movieDetail").length > 0) {
        $('#movieDetail').click(function() {
            $('#licenseContent').empty();
            var movieID = $('#MasterMovieID').val();
            $("#licenseContent").append('<img src="/themes/mofilm/images/loading.gif" alt="Wait" />');

            $.post(
                    "/admin/movieadmin/musicLicense/details/as.xml",
                    {
                        MovieID: movieID
                    },
            function(data) {
                handleLicenseData(data);
            },
                    'xml'
                    );
            return false;
        });
    }

    if ($('#videoLicense').length > 0) {
        movieID = $("#MasterMovieID").val();

        $.post(
                "/admin/movieadmin/musicLicense/details/as.xml",
                {
                    MovieID: movieID
                },
        function(data, textStatus) {
            handleLicenseData(data);
        },
                'xml'
                );

        $('#videoLicense').click(function() {
            $('#licenseContent').empty();
            $("#licenseContent").append('<img src="/themes/mofilm/images/loading.gif" alt="Wait" />');

            $.post(
                    "/admin/movieadmin/musicLicense/validate/as.xml",
                    {
                        MovieID: movieID
                    },
            function(data, textStatus) {
                handleLicenseData(data);
            },
                    'xml'
                    );
            return false;
        });
    }
    /*
     $('div.adminTags a').live("click",function(){
     $(this).parent().remove();
     $.post(
     "/videos/deleteMovieTag/as.json",
     {
     tagID: $(this).attr('href').slice(1),
     MovieID: $('#MasterMovieID').val(),
     tagCategory: $(this).parent().attr('id')
     },
     function(data) {
     if (data.status == 1) {
     $('div#newGenresTab table').append('<tr><td><input type="checkbox" value="'+data.id+'" name="Tags[]">'+data.name+'</td></tr>');
     }
     },
     'json'
     );
     });
     
     $('div#newGenresTab input').live("change",function(){
     $(this).parent().remove();
     $.post(
     "/videos/addMovieTag/as.json",
     {
     tagID: $(this).val(),
     MovieID: $('#MasterMovieID').val()
     },
     function(data) {
     if (data.status == 1) {
     $('div#genresTab').append('<div class="adminTags" id="adminGenres"><a href="#'+data.id+'">'+data.name+'<image src="/themes/mofilm/images/delete.gif" alt="Close" height="12" width="12"></a></div>')
     }
     },
     'json'
     );
     
     });
     */
    $('#grantsAvailable').change(function() {
        if ($('#grantsAvailable').val() == 'Y') {
            $('.grantsTab').show();
        }
        if ($('#grantsAvailable').val() == 'N') {
            $('.grantsTab').hide();
        }
    });

    $('#genPdf').click(function() {
        if ($("input[name='selectedpdfs[]']:checked").length > 0) {
            return true;
        } else {
            alert('Select atleast one application to generate PDF. ');
            return false;
        }
    });

    $('#sendEmail').click(function() {
        if ($("input[name='selectedpdfs[]']:checked").length > 0) {
            if (confirm('Acceptance Email will be sent only to Approved Applications')) {
                return true;
            } else {
                return false;
            }
        } else {
            alert('Select atleast one application to send Acceptance Email. ');
            return false;
        }
    });

    $('.uploadedFilesApproved').click(function() {
        res = confirm('Are you sure you wish to Approve #' + $(this).val());
        if (res) {
            uploadID = $(this).val();
            $.get(
                    "/uploadFiles/process",
                    {
                        Status: 'Approve',
                        fileID: uploadID
                    },
            function(data) {
                messageBox(data.status, data.message);
                $('#displayButtons' + uploadID).html('<button type="button" name="resend' + uploadID + '" value="' + uploadID + '" class="uploadedFilesReSend">Re-Send</button>');
                $('#uploadStatusDisplay' + uploadID).html('Approved');
            },
                    'json'
                    );
        }
        return false;
    });

    $('.uploadedFilesRejected').click(function() {
        res = confirm('Are you sure you wish to Reject #' + $(this).val());
        if (res) {
            uploadID = $(this).val();
            $.get(
                    "/uploadFiles/process",
                    {
                        Status: 'Reject',
                        fileID: uploadID
                    },
            function(data) {
                messageBox(data.status, data.message);
                $('#displayButtons' + uploadID).html('');
                $('#uploadStatusDisplay' + uploadID).html('Rejected');
            },
                    'json'
                    );
        }
        return false;
    });

    $('.uploadedFilesReSend').click(function() {
        res = confirm('Are you sure you wish to Re-Send Email for #' + $(this).val());
        if (res) {
            uploadID = $(this).val();
            $.get(
                    "/uploadFiles/process",
                    {
                        Status: 'ReSend',
                        fileID: uploadID
                    },
            function(data) {
                messageBox(data.status, data.message);
            },
                    'json'
                    );
        }
    });


    $('#root').change(function() {

        $.get(
                '/admin/other/momusic/typeLeaf/root/as.xml',
                {
                    RootID: $(this).val()
                },
        function(data, textStatus) {
            var htmlOptions = '';
            $(data).find('source').each(function() {
                htmlOptions += '<option value="' + $(this).find('id').text() + '">' + $(this).find('name').text() + '</option>';
            });
            $('#parent').html(htmlOptions);
        },
                'xml'
                );
    });

    $("#selectCategory1").change(function() {
        $(".shortlistedDisplay").show();
        $(".winnerDisplay").hide();
    });

    $("#selectCategory2").change(function() {
        $(".winnerDisplay").show();
        $(".shortlistedDisplay").hide();
    });



    if ($("#eventUpload").length > 0) {

        $('#eventUpload').change(function() {

            $.get(
                    '/admin/eventadmin/sourceManager/viewObjects/as.xml',
                    {
                        EventID: $(this).val()
                    },
            function(data, textStatus) {
                var htmlOptions = '';
                $(data).find('source').each(function() {
                    htmlOptions += '<option value="' + $(this).find('id').text() + '">' + $(this).find('name').text() + '</option>';
                });
                $('#sourceUpload').html(htmlOptions);
            },
                    'xml'
                    );
        });
    }

    if ($("#myVideoSave").length > 0) {
        $("#myVideoSave").live("click", function() {
            var $checkBoxes = $(".industry");
            var checkCount = 0;

            $checkBoxes.each(function() {
                if (this.checked)
                    checkCount++;
            });

            if (checkCount > 1) {
                $checkBoxes.removeAttr("checked");
                alert('Please select one Industry in Tags');
                return false;
            }
        });
        $("#movieDetailsForm").submit(function(e) {
            if ($("#broadcastDataChanged").val() == 'Changed') {
                
                if ($('.broadCastApproveddate').val() != '' || $('.broadCastNote').val() != '') {
                    $('input.broadCastdate').each(function(index) {
                        
                        if ($('.broadCastApproveddate').val() > $(this).val() && $(this).val() != '') {
                            message = mofilm.lang.messages.broadCastDateConflict;
                            $('#body div.container').append('<div id="formErrorBox" class="messageBox error"><p>' + message + '</p></div>');
                            $('#body div.container div.messageBox').delay(8500).slideUp(200);
                             e.preventDefault();
                            return false;
                        }
                        if ($.trim($(this).val()).length > 0 && $(this).parent().parent().children().children('p').children('select').val() == 0) {
                            message = mofilm.lang.messages.broadCastCountry;
                            $('#body div.container').append('<div id="formErrorBox" class="messageBox error"><p>' + message + '</p></div>');
                            $('#body div.container div.messageBox').delay(8500).slideUp(200);
                             e.preventDefault();
                            return false;
                        }
                        if ($(this).parent().parent().children().children('p').children('select').val() > 0 && ($(this).val() == '' || $(this).val() == 0)) {
                            message = mofilm.lang.messages.broadCastCountryDate;
                            $('#body div.container').append('<div id="formErrorBox" class="messageBox error"><p>' + message + '</p></div>');
                            $('#body div.container div.messageBox').delay(8500).slideUp(200);
                             e.preventDefault();
                            return false;
                        }

                    });
                }
                $('input.broadCastdate').each(function(index) {
                   if (($.trim($(this).val()).length > 0 || $(this).parent().parent().children().children('p').children('select').val() > 0) && $('.broadCastApproveddate').val() == '') {
                       message = mofilm.lang.messages.broadcastApproved;
                        $('#body div.container').append('<div id="formErrorBox" class="messageBox error"><p>' + message + '</p></div>');
                     $('#body div.container div.messageBox').delay(8500).slideUp(200);
                     e.preventDefault();
                        return false;
                }
                });
                var ids = [];
                $('input.broadCastdate').each(function(index) {
                    ids.push($(this).parent().parent().children().children('p').children('select').val());
                });
                orgLength = ids.length;
                if(orgLength > jQuery.unique( ids ).length) {
                    message = mofilm.lang.messages.broadCastDuplicate;
                    $('#body div.container').append('<div id="formErrorBox" class="messageBox error"><p>' + message + '</p></div>');
                    $('#body div.container div.messageBox').delay(8500).slideUp(200);
                    e.preventDefault();
                    return false;
                }   
            }
        });

    }

    if ($("#tagdialog").length > 0) {

        $("#contactUs").click(function() {
            $('#tagdialog').show();
            $('#tagdialog').dialog({'autoOpen': false, height: 400, width: 700, modal: true});
            $('#tagdialog').dialog('open');
            $('#tagdialog').dialog('option', 'title', 'Select Tags');
            $('#tagdialog').find('input:checkbox').removeAttr('checked');
            $('.industry').prop('disabled', false);
            return false;
        });

        $('.industry').change(function() {
            $('.industry').not(this).prop('disabled', this.checked);
        });

        $("#tagSubmitTop, #tagSubmitBottom").click(function() {

            var checkError = 0;
            if ($("[name='Tags[]']:checked").length == 0) {
                alert("Please select at least one tag.");
                checkError = 1;
            } else {
                var industryCount = 0;
                $('input.industry[type=checkbox]:checked').each(function() {
                    industryCount++;
                });
                if (industryCount > 1) {
                    alert("Please select only one tag from Industry.");
                    checkError = 1;
                }
            }

            if (checkError == 0) {

                var tagStr = '';
                $("[name='Tags[]']:checked").each(function() {
                    tagStr += $(this).val() + ' ';
                });
                $('#tagdialog').dialog('close');
                $("#Keywords").val(tagStr);
                $('#onlyTags').prop("checked", true);
                $("#videoSearch").submit();

            }

        });
    }
    $('#AllSelect').click(function() {
        var type_val = $("#AllSelect").val();
        if (type_val == 1)
        {
            $("input[name='selectedpdfs[]']").each(function()
            {
                this.checked = true;
            });
            $("#AllSelect").html('Un-Select All');
            $("#AllSelect").val("0");
            $("#AllSelect").check();
        }
        else
        {
            $("input[name='selectedpdfs[]']").each(function()
            {
                this.checked = false;
            });
            $("#AllSelect").html('Select All');
            $("#AllSelect").val("1");
            $("#AllSelect").uncheck();
        }
        return false;
    });

    $('#RejApplication').click(function() {
        var checked = []
        $("input[name='selectedpdfs[]']:checked").each(function()
        {
            checked.push(parseInt($(this).val()));
        });
        var id_length = checked.length;
        if (id_length == 0)
        {
            alert("There is no Application selected (Please select one or more)");
            return false;
        }
        else
        {
            var str = confirm("Rejecting an application triggers a rejection email to the filmmaker. Please remember to follow up with a personal email if possible. ");
            if (str == true)
                return true;
            else
                return false;
        }
    });

    if ($('#productListVideo').length > 0) {

        $("#projectdetails").hide();
        $("#projectlandingdetails").hide();

        $("#projectstatus").hide();
        var val = $("#productListVideo").val();
        if (val == 7) {
            $("#projectdetails").show();
            $("#projectlandingdetails").show();
            $("#projectstatus").show();

        }
        if(val == 3) {
            $("#projectdetails").hide();
            $("#projectstatus").show();
        }
            $("#projectstatus").show();
        $("#productListVideo").change(function() {
            var val = $("#productListVideo").val();
            if (val == 7) {
                $("#projectdetails").show();
                $("#projectstatus").show();
                $("#projectlandingdetails").show();


            } 
	    else if (val == 3) {
		$("#projectdetails").hide();
                $("#projectlandingdetails").hide();
		$("#projectstatus").show();
		}
		else {
                $("#DisplayName-project").val("default");
                $("#projectlandingdetails").hide();
                $("#projectdetails").hide();
                $("#projectstatus").show();

            }
        });

    }

    function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1);
        var sURLVariables = sPageURL.split('&');
        for (var i = 0; i < sURLVariables.length; i++)
        {
            var sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] == sParam)
            {
                return sParameterName[1];
            }
        }
    }

    if ($('#projectEventID').length > 0) {

        var param = getUrlParameter('eventID');
        if (param == undefined) {
            $(".brandDetails").hide();
        }

        if (param != undefined) {
            $("#projectEventID").val(param);
        }

        $("#projectEventID").change(function() {
            val = $("#projectEventID").val();
            window.location = "/admin/eventadmin/sourceManager/newObject?eventID=" + val;
        });

    }

    if ($('#branddetails').length > 0) {
        $('.save').click(function() {
            
            var pub = $("#public").val();
            if ( pub == "Y"){
                $('input[name="DisplayName"]').val('some value');
                $('textarea[name="Sitecopy"]').val('some value');
                $('textarea[name="Terms"]').val('some value');
            }
            
            $("#adminFormData").validate({
                rules: {
                    EventID:{
                        required: true,                        
                    },
                    SponsorID: {
                        required: true,
                        number: true
                    },
                    CorporateID: {
                        required: true,
                    },
                    BrandIDSelect: {
                        required: true,
                    },
                    EventID: {
                        required: true,
                        number: true
                    },
                    DisplayName: {
                        required: true,
                        minlength: 10
                    },
                    Sitecopy: {
                        required: true,
                    },
                    Terms: {
                        required: true,
                    }
                },
                messages: {
                    EventID: {
                        required: "*Select a valid Project",
                    },                    
                    SponsorID: {
                        required: "*Select a valid Sponsor",
                    },
                    CorporateID: {
                        required: "*Select a CorportateUD"
                    },
                    BrandIDSelect: {
                        required: "*Select a valid brandID"
                    },
                    EventID: {
                        required: "*Select a valid Sponsor",
                    },
                    DisplayName: {
                        required: "*Enter a valid page title"
                    },
                    Sitecopy: {
                        required: "*Enter a valid Sitecopy"
                    },
                    Terms: {
                        required: "*Enter a valid Terms"
                    }
                },
                invalidHandler: function(form, validator) {
                    if (validator.numberOfInvalids() > 0) {
                        validator.showErrors();
                        var index = $(":input.error").closest(".ui-accordion-content")
                                .index(".ui-accordion-content");
                        
        $('#body div.container').append('<div class="messageBox error"><p>' + "Fill all required fields" + '</p></div>');
        $('#body div.container div.messageBox').delay(2000).slideUp(2000);                                           
                        $("#userFormAccordion").accordion("activate", index);
                    }
                },
                submitHandler: function(form) {
                     var otherBudget = $('#EditVal').val();
                     var approvedAmt = $('#approvedAmt').val();
                    if ($('#approvedAmt').length > 0 && parseInt(otherBudget) != 0 && approvedAmt != '') {     
                        
                        if(parseInt(approvedAmt) <= parseInt(otherBudget)){
                            form.submit();
                        }else{
                            $('#EditVal').addClass('required integer error');
                            $("#EditVal").after('<label class="error" generated="true" for="BudgetOther">Enter a valid amount</label>');
                            var index = $('#EditVal .error').closest(".ui-accordion-content") .index(".ui-accordion-content");
                            $("#userFormAccordion").accordion("activate", index);
                            alert('The budget cannot be decreased as it has already been allocated.Please cancel or modify existing payments.');
                        }
                    }else{                
                        form.submit();
                    }
                }
            });
        });
    }
$('#cohort').append('<option value="alltime">  All time</option>');


});
