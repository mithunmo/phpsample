/**
 *  JS Resource for Credit/Contributors autocomplete
 *
 * @author Poulami Chakraborty
 * @copyright Mofilm Ltd
 * @package mofilm
 * @subpackage websites_mofilm.com_libraries
 * @version $Rev: 393 $
 */
jQuery(document).ready(function(){
    var today = new Date();
    if ($('#editPaymentForm').length) {
        $('.editDisable').prop("readonly", true);
    }
    var maxdate = '';  
    if ($('#addAdvanceForm').length) {
        var grantDueDateArray = $("#grantDueDate").val().split("/");
        maxdate = new Date(grantDueDateArray[2], (grantDueDateArray[0] -1), (grantDueDateArray[1]-10));
    }
    $('#addAdvanceForm #advanceDate').datepicker({
        minDate: 0,
        maxDate: maxdate
    });
     
    /******** Add Payment Page Validation Start ********/
    $("#dollar").on("input", function() {
        var paymentTotalVal;
        paymentTotalVal = this.value;
        if(paymentTotalVal == ''){
            paymentTotalVal = '0';
        }
        var paymentTotalStr = '$ '+paymentTotalVal;
        $('.paytotalnumber').html(paymentTotalStr);
        if($("#dollar").val() == '' || $("#dollar").val() == 0 || paymentTotalVal.length < 2) {
            $(".percentage").attr("disabled","disabled");
            $(".partPayment").attr("disabled","disabled");
        }else{
            $(".percentage").removeAttr("disabled"); 
            $(".partPayment").removeAttr("disabled","disabled");
        }
    });
    /* Add Payment Page dollar and percentage automation start */ 
    $('#PaymentNumber').live('change',function(){

        $('.datepicker').datepicker({
            minDate: today
        });
        $(".percentage").each(function() {
            var totalAmount = $("#dollar").val();
            var partID = $(this).attr('id');
            if($("#dollar").val() == '' || $("#dollar").val() == 0 || totalAmount.length < 2) {
                $("#"+partID).attr("disabled","disabled");
            }else{
                $("#"+partID).removeAttr("disabled"); 
            }
            
            $('#'+partID).bind('input', function() {
                var perVal =  $('#'+partID).val();
                var partAmount = (($("#dollar").val()/100) * perVal);
                var partIDArray = partID.split('_');
                var i = partIDArray[1];
                $('#partDollar_'+i).val(partAmount);
            });
        });
        $(".partPayment").each(function() {
            var totalAmount = $("#dollar").val();
            var partID = $(this).attr('id');
            if($("#dollar").val() == '' || $("#dollar").val() == 0 || totalAmount.length < 2) {
                $("#"+partID).attr("disabled","disabled");
            }else{
                $("#"+partID).removeAttr("disabled"); 
            }       
            $('#'+partID).bind('input', function() {  
                var perVal =  $('#'+partID).val();
                var percentage = (parseFloat(perVal/$("#dollar").val()) * 100);
                var partIDArray = partID.split('_');
                var i = partIDArray[1];
                $('#percentage_'+i).val(percentage);
            });         
        });
    });
    /* Add Payment Page dollar and percentage automation end */ 
    /* According to the payment parts number div will be recreated dynamically start*/
    $("#PaymentNumber").change(function() {
        $("#errorAmount").html('');
        $("#errorAmount").hide();  
        var paymentNumber = $('#PaymentNumber').val();
        var singlePaymentStr = '<div class="row" style="padding:0px 15px 0px 15px;" style="display:hidden;">\n\
                                    <div class="col-md-4">\n\
                                    <label class="paylabel">Time:</label>\n\
                                    <select id="DateOn_1" name="DateOn_1" class="form-control">\n\
                                        <option value="DateOn">On</option>\n\
                                    </select>\n\
                                    </div>\n\
                                    <div class="col-md-4" style="margin:20px 0px 20px 0px; ">\n\
                                        <select id="DateCondition_1" name="DateCondition_1" class="form-control">\n\
                                            <option value="DateOf">Date Of</option>\n\
                                        </select>\n\
                                    </div>\n\
                                    <div class="col-md-4" style="margin:15px 0px 20px 0px; ">\n\
                                        <div class="input-group date" id="datetimepicker1" style="margin-top: 5px;">\n\
                                            <span class="input-group-addon" >\n\
                                                <span class="dataicon"><img src="/themes/mofilm/images/payment/dateicon.jpg" /></span>\n\
                                            </span>\n\
                                            <input style="width:115px;" id="datechoice_1" class="datepicker form-control" type="text"   name="datechoice_1"  >\n\
                                        </div>\n\
                                        <div id="errordatechoice_1" class="paymenterror"></div>\n\
                                    </div>\n\
                                </div>';
        
        var multiPaymentStr = '';
       
        if(paymentNumber > 1){
            
            multiPaymentStr += '<div class="paymenthead"><h5>Payment Schedule:</h5></div>';
            
            for(var i=1; i<=paymentNumber; i++){
                if(i> 1){
                    multiPaymentStr += '<div class="col-md-12"><hr class="paymenthr"/></div>';
                }               
                multiPaymentStr += '<div class="row" style="padding:10px 20px;">\n\
                                        <div class="col-md-4">\n\
                                            <label class="paylabel">P'+i+':</label>\n\
                                            <div class="input-group">\n\
                                                <span class="input-group-addon">$</span>\n\
                                                <input id="partDollar_'+i+'" onkeydown="numericOnly(this,event)" name="partPaymentAmount_'+i+'" class="form-control partPayment" placeholder="" type="text">\n\
                                            </div>\n\
                                             <div id="errorpartDollar_'+i+'"  class="paymenterror"></div>\n\
                                        </div>\n\
                                        <div class="col-md-1" style="margin:28px 0px 20px 0px;  max-width: 40px; "> \n\
                                            <p class="paymenttxt">/</p>\n\
                                        </div>\n\
                                        <div class="col-md-3" style="margin:20px 0px 20px 0px; "> \n\
                                            <div class="input-group">\n\
                                                <input id="percentage_'+i+'" name="partPaymentPercentage_'+i+'" class="form-control percentage" placeholder="" type="text">\n\
                                                <span class="input-group-addon">%</span>\n\
                                            </div>\n\
                                        </div>\n\
                                    </div>\n\
                                    <div class="row" style="padding:10px 20px;">\n\
                                        <div class="col-md-4">\n\
                                            <label class="paylabel">Time:</label>\n\
                                            <select id="DateOn_'+i+'" name="DateOn_'+i+'" class="form-control">\n\
                                                <option value="DateOn">On</option>\n\
                                            </select>\n\
                                        </div>\n\
                                        <div class="col-md-4" style="margin:20px 0px 20px 0px; " >\n\
                                            <select id="DateCondition_'+i+'" name="DateCondition_'+i+'" class="form-control">\n\
                                                <option value="DateOf">Date Of</option>\n\
                                            </select>\n\
                                        </div>\n\
                                        <div class="col-md-4" style="margin:15px 0px 20px 0px; ">\n\
                                            <div class="input-group date" id="datetimepicker1" style="margin-top: 5px;">\n\
                                                <span class="input-group-addon" >\n\
                                                    <span class="dataicon"><img src="/themes/mofilm/images/payment/dateicon.jpg" /></span>\n\
                                                </span>\n\
                                                <input style="width:115px;" id="datechoice_'+i+'" class="datepicker form-control" type="text"   name="datechoice_'+i+'"  >\n\
                                            </div>\n\
                                            <div id="errordatechoice_'+i+'" class="paymenterror"></div>\n\
                                        </div>\n\
                                    </div>';
            }
        }
 
        if(paymentNumber == 1){
            $("#singlePayment").html(singlePaymentStr);
            $("#multiplePayment").html('');
        }else{
            $("#singlePayment").html('');
            $("#multiplePayment").html(multiPaymentStr);
        }
    });
    /* According to the payment parts number div will be recreated dynamically end*/
    /******** Add Payment Page Validation Start ********/
   
    /******** Edit ADhoc Payment Page Validation Start ********/
    /* Display of edit adhoc page load, showing the series dynamically */   
    $('#PaymentNumberAdhoc').on('change',function(){
    	var totalParts = $("#PaymentNumberAdhoc").val();
        var availableParts = $("#availableParts").val();
        var multiPaymentStr =''; 
        availableParts++;
        $("#dynamicPaymentDiv").html('');
        $("#totalParts").val(totalParts);
        for(var i=availableParts; i<=totalParts; i++){         
            multiPaymentStr += '<div class="col-md-12"><hr class="paymenthr"/></div>';              
            multiPaymentStr += '<div class="row" style="padding:10px 20px;">\n\
                                    <div class="col-md-4">\n\
                                        <label class="paylabel">P'+i+':</label>\n\
                                        <div class="input-group">\n\
                                            <span class="input-group-addon">$</span>\n\
                                            <input id="partDollar_'+i+'" onkeydown="numericOnly(this,event)" name="partPaymentAmount_'+i+'" class="form-control partPayment" placeholder="" type="text">\n\
                                        </div>\n\
                                         <div id="errorpartDollar_'+i+'"  class="paymenterror"></div>\n\
                                    </div>\n\
                                    <div class="col-md-1" style="margin:28px 0px 20px 0px;  max-width: 40px; "> \n\
                                        <p class="paymenttxt">/</p>\n\
                                    </div>\n\
                                    <div class="col-md-3" style="margin:20px 0px 20px 0px; "> \n\
                                        <div class="input-group">\n\
                                            <input id="percentage_'+i+'" name="partPaymentPercentage_'+i+'" class="form-control percentage" placeholder="" type="text">\n\
                                            <span class="input-group-addon">%</span>\n\
                                        </div>\n\
                                    </div>\n\
                                </div>\n\
                                <div class="row" style="padding:10px 20px;">\n\
                                    <div class="col-md-4">\n\
                                        <label class="paylabel">Time:</label>\n\
                                        <select id="DateOn_'+i+'" name="DateOn_'+i+'" class="form-control">\n\
                                            <option value="DateOn">On</option>\n\
                                        </select>\n\
                                    </div>\n\
                                    <div class="col-md-4" style="margin:20px 0px 20px 0px; " >\n\
                                        <select id="DateCondition_'+i+'" name="DateCondition_'+i+'" class="form-control">\n\
                                            <option value="DateOf">Date Of</option>\n\
                                        </select>\n\
                                    </div>\n\
                                    <div class="col-md-4" style="margin:15px 0px 20px 0px; ">\n\
                                        <div class="input-group date" id="datetimepicker1" style="margin-top: 5px;">\n\
                                            <span class="input-group-addon" >\n\
                                                <span class="dataicon"><img src="/themes/mofilm/images/payment/dateicon.jpg" /></span>\n\
                                            </span>\n\
                                            <input style="width:115px;" id="datechoice_'+i+'" class="datepicker form-control" type="text"   name="datechoice_'+i+'"  >\n\
                                        </div>\n\
                                        <div id="errordatechoice_'+i+'" class="paymenterror"></div>\n\
                                    </div>\n\
                                </div>\n\
                                <input type="hidden"  name="paymentID_'+i+'" value="0" />';
        }  
        $("#dynamicPaymentDiv").append(multiPaymentStr);
        $('.datepicker').datepicker({
            minDate: today
        });

        $(".percentage").each(function() {
            var totalAmount = $("#editdollar").val();
            var partID = $(this).attr('id');
            if($("#editdollar").val() == '' || $("#editdollar").val() == 0 || totalAmount.length < 2) {
                $("#"+partID).attr("disabled","disabled");
            }else{
                $("#"+partID).removeAttr("disabled"); 
            }
            
            $('#'+partID).bind('input', function() {
                var perVal =  $('#'+partID).val();
                var partAmount = (($("#editdollar").val()/100) * perVal);
                var partIDArray = partID.split('_');
                var i = partIDArray[1];
                $('#partDollar_'+i).val(partAmount);
            });
        });
        $(".partPayment").each(function() {
            var totalAmount = $("#editdollar").val();
            var partID = $(this).attr('id');
            if($("#editdollar").val() == '' || $("#editdollar").val() == 0 || totalAmount.length < 2) {
                $("#"+partID).attr("disabled","disabled");
            }else{
                $("#"+partID).removeAttr("disabled"); 
            }        
            $('#'+partID).bind('input', function() {                  
                var perVal =  $('#'+partID).val();
                var percentage = (parseFloat(perVal/$("#editdollar").val()) * 100);
                var partIDArray = partID.split('_');
                var i = partIDArray[1];
                $('#percentage_'+i).val(percentage);
            });         
        });
    }); 
     
    /* on change of given total amount, down display html will change, 
     * and it will also effect on part amount*/  
    $("#editdollar").on("change", function() {
        paymentTotalVal = this.value;
        if($("#editdollar").val() == '' || $("#editdollar").val() == 0 || paymentTotalVal.length < 2) {
            $(".percentage").each(function() {
                var perID = $(this).attr('id');
                $('#'+perID).val('');            
            });
            $(".percentage").attr("disabled","disabled");
        }else{
            $(".percentage").each(function() {
                var totalAmount = $("#editdollar").val();
                var partID = $(this).attr('id');
                var partIDArray = partID.split('_');
                var i = partIDArray[1];

                var dateEntered = $("#datechoice_"+i).val();
                var month = dateEntered.substring(5,7);
                var date = dateEntered.substring(8, 10);
                var year = dateEntered.substring(0, 4);
                var dateToCompare = new Date(year, month - 1, date);
                var currentDate = new Date();
                currentDate.setHours(0,0,0,0);   

                if (dateToCompare > currentDate) {
                    if($("#editdollar").val() == '' || $("#editdollar").val() == 0 || totalAmount.length < 2) {
                        $("#"+partID).attr("disabled","disabled");
                    }else{
                        $("#"+partID).removeAttr("disabled"); 
                    }
                    $('#'+partID).bind('input', function() {
                        var perVal =  $('#'+partID).val();
                        var partAmount = (($("#editdollar").val()/100) * perVal);                  
                        $('#partDollar_'+i).val(partAmount);
                    });
                }
            });
            $(".partPayment").each(function() {
                var totalAmount = $("#editdollar").val();
                var partID = $(this).attr('id');
                var partIDArray = partID.split('_');
                var i = partIDArray[1];
                
                var dateEntered = $("#datechoice_"+i).val();
                var month = dateEntered.substring(5,7);
                var date = dateEntered.substring(8, 10);
                var year = dateEntered.substring(0, 4);
                var dateToCompare = new Date(year, month - 1, date);
                var currentDate = new Date();
                currentDate.setHours(0,0,0,0);   
                
                if (dateToCompare > currentDate) {
                    if($("#editdollar").val() == '' || $("#editdollar").val() == 0 || totalAmount.length < 2) {
                        $("#"+partID).attr("disabled","disabled");
                    }else{
                        $("#"+partID).removeAttr("disabled"); 
                    }
                    $('#'+partID).bind('input', function() {                  
                        var perVal =  $('#'+partID).val();
                        var percentage = (parseFloat(perVal/$("#editdollar").val()) * 100);                 
                        $('#percentage_'+i).val(percentage);
                    });
                }
                
            });
        }
    });
    /******** Edit ADhoc Payment Page Validation End ********/
    
    $('#searchButtonSubmit').click(function(){
        if($("#contributors").val() == ''){
            $("#FilmMaker").val('');
        }
    });
    
    $(".tabsection ul li a").click(function(e) {
        e.preventDefault();
        var liNumber = $(this).attr('id');
        var partIDArray = liNumber.split('-');
        var divID = 'tabs-'+partIDArray[1];   
        $('#'+ divID).siblings('div').hide();
        $('#'+ divID).show(); 
    });
    
     $("#subtabs ul li a").click(function(e) {
        e.preventDefault();
        var liNumber = $(this).attr('id');
        var partIDArray = liNumber.split('-');
        var divID = 'subtabs-'+partIDArray[1];  
        $('#'+ divID).siblings('div').hide();
        $('#'+ divID).show();
    });  
    
    /* Validate Advance payment form*/
    $('#advanceSave').click(function(){

        var error = 0;
        if($("#advanceAmount").val() == '' || parseInt($("#advanceAmount").val()) === 0 ) {
            $("#errorAdvanceAmount").show();
            $("#errorAdvanceAmount").html('Please give advance amount');  
            error=1;   
        }else{
            if(parseInt($("#advanceAmount").val()) >= parseInt($("#grantAmount").val())) {
                $("#errorAdvanceAmount").show();
                $("#errorAdvanceAmount").html('Please give advance amount less than granted amount'); 
                error=1;   
            }else{
                $("#errorAdvanceAmount").html('');
                $("#errorAdvanceAmount").hide();   
            } 
        }
        if($("#advanceDate").val() == '') {
            $("#errorAdvanceDate").show();
            $("#errorAdvanceDate").html('Please give advance date'); // show Warning  
            error=1;   
        }else{          
                var dateEntered = $("#advanceDate").val();
                var month = dateEntered.substring(0, 2);
                var date = dateEntered.substring(3, 5);
                var year = dateEntered.substring(6, 10);

                var dateToCompare = new Date(year, month - 1, date);
                var currentDate = new Date();
                currentDate.setHours(0,0,0,0);

                if (dateToCompare < currentDate) {
                    $("#errorAdvanceDate").show();
                    $("#errorAdvanceDate").html('Please select date greater than today'); // show Warning  
                    error=1;   
                }else{
                    var grantDueDateArray = $("#grantDueDate").val().split("/");
                    var maxdate = new Date(grantDueDateArray[2], (grantDueDateArray[0] -1), (grantDueDateArray[1] - 10));
                    if (dateToCompare > maxdate) {
                        $("#errorAdvanceDate").show();
                        $("#errorAdvanceDate").html('Please select date less than Grant due date.'); // show Warning  
                        error=1;   
                    }else{
                        $("#errorAdvanceDate").html('');
                        $("#errorAdvanceDate").hide();
                    }
                } 
        }
        if(error == 0){
            $('#addAdvanceForm').submit();
        }else{
            return false;
        }
        
    });
    
    $('#saveEditButton').click(function(){
       $('#editForm').submit();
    });   
    
    $('#button2idDraft').click(function(){
       
        if(confirm("Are you sure to change the status?")) {
            var hrefURL = $(this).parent().attr('href');
            $('#editForm').attr('action', hrefURL);
            $("#editForm").submit();
        }else{
            return false;
        }
       
    });
    
    /* Validate Ad hoc Add and edit payment form Start*/
    $('#button2id').click(function(){
        var error = 0;
        if($("select[name=EventID]").val() == 0) {
            $("#errorEvent").show();
            $("#errorEvent").html('Please select event'); // show Warning  
            error=1;   
        }else{
            $("#errorEvent").html('');
            $("#errorEvent").hide();   
        }
        if($("select[name=BrandID]").val() == 0) {
            $("#errorBrand").show();
            $("#errorBrand").html('Please select brand'); // show Warning  
            error=1;   
        }else{
            $("#errorBrand").html('');
            $("#errorBrand").hide();
        }
        
        if($("#contributors").val() == '') {
            $("#errorFilmMaker").show();
            $("#errorFilmMaker").html('Please select film maker'); // show Warning  
            error=1;   
        }else{
            $("#errorFilmMaker").html('');
            $("#errorFilmMaker").hide();
        }
       
        if($("select[name=PaymentType]").val() == 0) {
            $("#errorPaymentType").show();
            $("#errorPaymentType").html('Please select Payment Type'); // show Warning  
            error=1;   
        }else{
            $("#errorPaymentType").html('');
            $("#errorPaymentType").hide();
        }
       
        var totalAmount = $("input[name=TotalPayment]").val();
        if($("input[name=TotalPayment]").val() == '' || $("input[name=TotalPayment]").val() == 0 || totalAmount.length < 2) {
            $("#errorTotalAmount").show();
            $("#errorTotalAmount").html('Please give total amount'); // show Warning  
            error=1;   
        }else{
            $("#errorTotalAmount").html('');
            $("#errorTotalAmount").hide();
        }
        
        if($("select[name=Paymentnumber]").val() == 0) {
            $("#errorPaymentNumber").show();
            $("#errorPaymentNumber").html('Please select payment parts'); // show Warning  
            error=1;   
        }else{
            $("#errorPaymentNumber").html('');
            $("#errorPaymentNumber").hide();
        }
        
        $(".datepicker").each(function() {
            var dateID = $(this).attr('id');
            var partIDArray = dateID.split('_');
            var i = partIDArray[1];
            var partStatus = $('#status_'+i).val();
            if(partStatus != 'Canceled' && partStatus != 'undefined'){
                if($("#"+dateID).val() == 0) {
                    $("#error"+dateID).show();
                    $("#error"+dateID).html('Please select date'); // show Warning  
                    error=1;   
                }else{
                    var dateEntered = $("#"+dateID).val();
                    var month = dateEntered.substring(0, 2);
                    var date = dateEntered.substring(3, 5);
                    var year = dateEntered.substring(6, 10);

                    var dateToCompare = new Date(year, month - 1, date);
                    var currentDate = new Date();
                    currentDate.setHours(0,0,0,0);

                    if (dateToCompare < currentDate) {
                        $("#error"+dateID).show();
                        $("#error"+dateID).html('Please select date greater than today'); // show Warning  
                        error=1;   
                    }else{
                        $("#error"+dateID).html('');
                        $("#error"+dateID).hide();
                    }
                }
            }
        });
        var totalPartAmt = 0;
        $(".partPayment").each(function() {
            var partID = $(this).attr('id');
            if($("#"+partID).val() == 0) {
                $("#error"+partID).show();
                $("#error"+partID).html('Please give part amount'); // show Warning  
                error=1;   
            }else{
                $("#error"+partID).html('');
                $("#error"+partID).hide();
            }
            var partIDArray = partID.split('_');
            var i = partIDArray[1];
            var partStatus = $('#status_'+i).val();
            if(partStatus != 'Canceled'){
                totalPartAmt = parseInt($("#"+partID).val()) + parseInt(totalPartAmt);
            }
        });
        if($("#PaymentNumber").val() > 1 || $("#PaymentNumberAdhoc").val() > 1){
            if($("input[name=TotalPayment]").val() != '' || $("input[name=TotalPayment]").val() != 0 || totalAmount.length > 2) {
                if($("input[name=TotalPayment]").val() == parseInt(totalPartAmt)) {
                    $("#errorAmount").html('');
                    $("#errorAmount").hide();        
                }else{
                    $("#errorAmount").show();
                    $("#errorAmount").html('Amount missmatch'); // show Warning  
                    error=1;   
                }
            }
        }
        if(error == 0){
            $('#addPaymentForm').submit();
            $('#editPaymentForm').submit();
        }else{
            return false;
        }
    });
    /* Validate Ad hoc Add and edit payment form End*/
    
    $('.paymentEditCancel').click(function(){
        if ($('#editForm').length) {
            $("#editForm")[0].reset();
        }
    });
    
    /*** On Selecting project, brand will be changed accordingly ****/
    if ($('#paymentEventList').length > 0) {
        $('#paymentEventList').change(function() {
            $.get(
                    '/admin/eventadmin/brand/viewObjects/as.xml',
                    {
                        Offset: 0,
                        Search: 'Search',
                        EventID: $(this).val()
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
            
        });
    }
    

});
function HandleBrowseClick(){
    
    var fileinput = document.getElementById("browse");
    fileinput.click();
    var fileVal;
    $('input[type=file]').change(function(e){
        fileVal =  $('input[type=file]').val();
        var textinput = document.getElementById("filename");
        textinput.value = fileinput.value;
        $("#showFilename").html(fileVal);
    });
 
}

function clearAdvanceForm(){
    $("#addAdvanceForm")[0].reset();
    $(".paymenterror").html('');
    $(".paymenterror").hide();      
    return false;
}

function clearFinanceForm(){
    $("#editPaymentForm")[0].reset();
    $(".paymenterror").html('');
    $(".paymenterror").hide();    
    return false;
}

function validateFinanceForm(){

    var error = 0;
    if($("#payableAmount").length > 0){
        var payableAmount = $("#payableAmount").val();
        if($("#payableAmount").val() == '' || $("#payableAmount").val() == 0 || payableAmount.length < 2) {
            $("#errorPayableAmount").show();
            $("#errorPayableAmount").html('Please give amount'); // show Warning  
            error=1;   
        }else{
            $("#errorPayableAmount").html('');
            $("#errorPayableAmount").hide();
        }
    }
    
    if($("#tilldate").length > 0){
        if($("#tilldate").val() == 0) {
            $("#errorDueDate").show();
            $("#errorDueDate").html('Please select date'); // show Warning  
            error=1;   
        }else{
            var dateEntered = $("#tilldate").val();
            var month = dateEntered.substring(5, 7);
            var date = dateEntered.substring(8, 10);
            var year = dateEntered.substring(0, 4);

            var dateToCompare = new Date(year, month - 1, date);
            var currentDate = new Date();
            currentDate.setHours(0,0,0,0);
            if (dateToCompare < currentDate) {
                $("#errorDueDate").show();
                $("#errorDueDate").html('Please select date greater than today'); // show Warning  
                error=1;   
            }else{
                $("#errorDueDate").html('');
                $("#errorDueDate").hide();
            }
        }
    }

    if(error == 0){
        return true;
    }else{
        return false;
    }
}
function numericOnly(element,e){
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
        // Allow: Ctrl+A
        (e.keyCode == 65 && e.ctrlKey === true) ||
        // Allow: home, end, left, right
        (e.keyCode >= 35 && e.keyCode <= 39)) {
        // let it happen, don't do anything
        return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
}
