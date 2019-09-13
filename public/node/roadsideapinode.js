/**
 * Created by nomantufail on 10/27/2016.
 */
var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var db = require('./db.js');
var request = require("request");
var mydb = new db();

app.get('/', function (req, res) {
    res.send('Working fine New');
});
var sockets = {};
var arr = [];
io.on('connection', function (socket) {
    // socket.on('location_get', function (data) {
    //     let x = 10;
    //     let y = 10;
    //     function sleep(ms) {
    //       return new Promise(resolve => setTimeout(resolve, ms));
    //     }

    //     async function demo() {
    //         while(1){
    //             io.emit('location_send', {'job_id': data.job_id, 'lat': /*data.lat*/x, 'lng': /*data.lng*/y});
    //             await sleep(3000);
        
    //             // setTimeout(() => {
    //             //     io.emit('location_send', {'job_id': data.job_id, 'lat': /*data.lat*/x, 'lng': /*data.lng*/y});
        
    //             // }, 3000);
    //         /*x = x+1;*/
    //     }

          
          
         
    //     }

    //     demo();

    //     // while(x < 100){
    //     //         io.emit('location_send', {'job_id': data.job_id, 'lat': /*data.lat*/x, 'lng': /*data.lng*/y});
    //     //         await sleep(2000);
        
    //     //         setTimeout(() => {
    //     //             io.emit('location_send', {'job_id': data.job_id, 'lat': /*data.lat*/x, 'lng': /*data.lng*/y});
        
    //     //         }, 1000);
    //     //     x = x+1;}


    // });

    function sleep(ms) {
              return new Promise(resolve => setTimeout(resolve, ms));
            }

    socket.on('location_get', (data) => {
	
        var request = require("request");
        var options = { method: 'POST',
        url: 'https://staging.joinswoop.com/graphql',
        headers: 
        { 'Postman-Token': '32511e4e-7cea-4280-a8bb-04c99a7cec8c',
            'cache-control': 'no-cache',
            Authorization: 'Bearer AKgQquPL5ws-TcEF_jdr7L20pACQ3Kv3qPz8t4aBZhU',
            'content-type': 'multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW' },
        formData: { query: 'query {  job(id:"'+data.job_id+'") {   partner {     vehicle {          location {         lat          lng        }     }    }  }}' } };
	
	// io.emit("location_send", 'check2');
 //        return true;

        request(options, function (error, response, body) {
        if (error) {
            console.log(error)
        }

        dataRes = JSON.parse(body)
        console.log(dataRes)
            to_return = {}
            to_return['job_id'] = data.job_id
            to_return['user_id'] = data.user_id

        flag = false
        if(dataRes.data.job != null){
            if(dataRes.data.job.partner != null){
            if(dataRes.data.job.partner.vehicle != null){
                if(dataRes.data.job.partner.vehicle.location != null){
                    to_return["lat"] = dataRes.data.job.partner.vehicle.location.lat
                    to_return["lng"] = dataRes.data.job.partner.vehicle.location.lng
                    flag = true
                }
            }
        }
    }

        if(!flag){
            to_return["lat"] = 'null'
            to_return["lng"] = 'null'

        }

            io.emit("location_send", to_return);
            
        });

          
    });
    socket.on('disconnect', function () {
        if (sockets[socket.id] != undefined) {
            mydb.releaseRequest(sockets[socket.id].user_id).then(function (result) {
                console.log('disconected: ' + sockets[socket.id].request_id);
                io.emit('request-released', {
                    'request_id': sockets[socket.id].request_id
                });
                delete sockets[socket.id];
            });
        }
    });
});

http.listen(5005, function () {
    console.log('listening on *:5005');
});