{include file=$oView->getTemplateFile('header', 'shared') pageTitle=''}
{include file=$oView->getTemplateFile('menu', 'shared')}
<style>
    .hclass {
    background: #F3F4F5 none repeat scroll 0% 0%;
    color: #535659;
    font-weight: bold;
    padding: 10px 20px;
    text-align: left;
    border-bottom: 1px solid #CCC;
    vertical-align: top;
    font-size: 13px;
    font-family: "Open Sans";
}

</style>
<link rel="stylesheet" type="text/css" href="/themes/mofilm/css/payment.css?6883878797" media="screen" />
 <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript">
$(function () {
    function addZero(i) {
    if (i < 10) {
        i = "0" + i;
    }
    return i;
}
    $("#fileUpload").bind("change", function () {
        var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.csv|.txt)$/;
        if (regex.test($("#fileUpload").val().toLowerCase())) {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var table = $("<table class='payformtable' style='overflow:auto;' />");
                    var rows = e.target.result.split("\n");
                    var headerInfo = rows[0].split(",");
                    
                    for (var i = 0; i < rows.length-1; i++) {
                        if(i==0)
                            var row = $("<tr class='hclass'/>"); 
                        else    
                            var row = $("<tr />");    
                        var cells = rows[i].split(",");
                        for (var j = 0; j < cells.length; j++) {
                            if(i==0)
                                var cell = $("<td style='background: #F3F4F5' />");
                            else
                                var cell = $("<td />");
                            //alert(headerInfo[j]);
                            headerInfo[j] = headerInfo[j].replace('"','');
                            headerInfo[j] = headerInfo[j].replace(' ','');
                            headerInfo[j] = $.trim(headerInfo[j]);
                            var columnName = headerInfo[j];
                            var rowIndex;
                            var d = new Date();
                            
                            var strDate = addZero(d.getMonth()+1)+ "/" + addZero(d.getDate()) + "/" + d.getFullYear();
                            if(i > 0)
                            {
                                rowIndex = i-1;
                                if(columnName == "PaymentID")
                                cells[j] = "#"+cells[j];
                                if(columnName == "PaidDate") 
                                cells[j] = strDate;
                                if(columnName == "Status") 
                                cells[j] = "<input style='width:80px;' name='PaymentArr["+rowIndex+"]["+columnName+"]' type='text' value='"+cells[j]+"' >";
                                if(columnName == "HashID")
                                cells[j] = "<input type='hidden' name='PaymentArr["+rowIndex+"]["+columnName+"]' value='"+cells[j].replace("'", "")+"' > "+cells[j];    
                                if(columnName == "Amount(USD)")
                                cells[j] = "<input type='hidden' name='PaymentArr["+rowIndex+"][Amount]' value='"+cells[j]+"' > "+cells[j];    
                            }   
                            cell.html(cells[j]);
                            row.append(cell);
                        }
                        table.append(row);
                    }
                    $("#dvCSV").html('');
                    $("#dvCSV").append(table);
                    $("#FormTable").css("display","block");
                }
                reader.readAsText($("#fileUpload")[0].files[0]);
            } else {
                alert("This browser does not support HTML5.");
            }
        } else {
            alert("Please upload a valid CSV file.");
        }
    });
});
</script>
<div id="body">
  
     <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1">
    <div style="max-width:1000px; margin:0px auto; padding-top: 40px;">
        <div style="margin-bottom: 20px;">
        <h2>Import List</h2>
        </div>
         <input type="file" id="fileUpload" />
<hr />

<form name="confirmForm" id="confirmForm" method="post" action="/admin/paymentDetails/confirmPayment" >
                        
<div id="FormTable" style="display:none">
<div class="syncwrap"> 
                <div class="synccontent">
                    <p>Downlod CSV for payments shown,update with bank data, and import to update record.</p> 
                    <div class="syncright">
                        <a href="/admin/paymentDetails/viewFinance" class="btn btn-cancel" style="color:white;" >Cancel</a>
                        <button id="save" name="Save" value="Confirm" class="btn btn-save" type="submit">Save</button> 
                    </div>             
                </div> 
                
</div>
             
<div id="dvCSV">
    
</div>
    
  
</div>   
    
</form>
<br> <br>
    </div>
    </div>

{include file=$oView->getTemplateFile('footer', 'shared')}


