var mysql = require("mysql");
var request = require('request');

var env = process.argv[2] || 'dev';
switch (env) {
    case 'dev':
        console.log("dev");
        var connection = mysql.createConnection({
            user: "root",
            password: "yourpassword",
            database: "mofilm_content"
        });
        break;
    case 'stage':
        var connection = mysql.createConnection({
            user: "root",
            password: "password",
            database: "mofilm_content"
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
            database: "mofilm_content",
            insecureAuth: true

        });
        break;
}


// Message object

function loop() {
    console.log("Reading from DB");
    connection.query('SELECT * FROM userNotification where status = 0 limit 1;', function(error, rows, fields) {

        if (rows.length == 1) {

            var sourceid = rows[0]["sourceID"];
            var title = rows[0]["title"];

            connection.query('SELECT * FROM userMobile ;', function(errorN, rowsN, fieldsN) {

                for (i = 0; i < rowsN.length; i++) {

                    request.post(
                            'https://api.cloud.appcelerator.com/v1/push_notification/notify_tokens.json?key=s4dGDJ5r61jBlscy9mZa4vBxZTuRtoY8',
                            {form: {
                                    channel: 'test',
                                    to_tokens: rowsN[i]["token"],
                                    payload: {'id': sourceid, 'alert': title, 'badge' :  '1', 'icon' : 'appicon', 'sound': 'default' }
                                }
                            },
                    function(error, response, body) {
                        if (!error && response.statusCode == 200) {
                            console.log(body)
                        }
                    }
                    );
                }
            });


            connection.query('update userNotification set status = 1 where id=' + rows[0]["id"] + ';', function(errorq, rowsq, fieldsq) {
                console.log("calling here from inside");
                errorq = null;
                rowsq = null;
                fieldsq = null;
                setTimeout(loop, 2000);
            });


        } else {
            rows = null;
            fields = null;
            error = null;
            console.log("calling here from outside");
            setTimeout(loop, 2000);
        }

    });


}

loop();
