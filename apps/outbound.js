var nodemailer = require("nodemailer");
var mysql = require("mysql");

// Create an Amazon SES transport object
var transport = nodemailer.createTransport("SES", {
    AWSAccessKeyID: "AKIAI4HCNO3U37FJLVNA",
    AWSSecretKey: "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",
    ServiceUrl: "https://email.us-east-1.amazonaws.com" // optional
});

console.log('SES Configured');

var env = process.argv[2] || 'dev';
switch (env) {
    case 'dev':
        console.log("dev");
        var connection = mysql.createConnection({
            user: "root",
            password: "yourpassword",
            database: "mofilm_messages"
        });
        break;
    case 'stage':
        var connection = mysql.createConnection({
            user: "root",
            password: "password",
            database: "mofilm_messages"
        });
        break;
    case 'cloud':
        var connection = mysql.createConnection({
            user: "root",
            password: "xVpa1c7q",
            database: "mofilm_messages"
        });    
    break;
    case 'prod':
        var connection = mysql.createConnection({
            host: '192.168.40.9',
            user: "mofilm",
            password: "9Barton1",
            database: "mofilm_messages",
            insecureAuth: true

        });
        break;
}


// Message object

function loop() {
//    console.log("Reading from DB");
    connection.query('SELECT * FROM outboundMessagesQueue limit 1;', function(error, rows, fields) {

        if (rows.length == 1) {

            var messageID = rows[0]["messageID"];
            
            console.log(messageID);
            connection.query('SELECT * FROM outboundMessages inner join outboundMessagesParams on outboundMessages.messageID = outboundMessagesParams.messageID  where outboundMessagesParams.messageID = '+messageID + ' ;', function(error, rowsq, fields) {

            console.log(rowsq[1]);

            
        if( rowsq.length == 3 || rowsq.length == 4 ) {		
            
			var message = {
				from: 'MOFILM <no-reply@mofilm.com>',
				to: rowsq[0]["recipient"],
				subject: rowsq[2]["paramValue"],
				text: rowsq[0]["paramValue"],
				html: rowsq[0]["paramValue"]
			};


        }
      
      console.log(message);

/*
                var message = {
                    from: 'MOFILM <no-reply@mofilm.com>',
                    to: rows[0]["to"],
                    subject: rows[0]["subject"],
                    text: rows[0]["body"],
                    html: rows[0]["body"]
                };
*/
                console.log('Sending Mail');

                transport.sendMail(message, function(error) {
                    if (error) {
                        console.log('Error occured');
                        console.log(error.message);
                        //return;
                    }

                    console.log('Message sent successfully!');


                    connection.query('delete from outboundMessagesQueue  where messageID=' + rows[0]["messageID"] + ';', function(errorq, rowsq, fieldsq) {
                        message = null;
                        error = null;
                        errorq = null;
                        rowsq = null;
                        rows = null;
                        fieldsq = null;
                        fields = null;
               //         console.log("calling here from inside");
                        setTimeout(loop, 2000);
                    });


                });




            });
        } else {
            message = null;
            rows = null;
            fields = null;
            error = null;
          //  console.log("calling here from outside");
            setTimeout(loop, 2000);
        }

    });


}

loop();
