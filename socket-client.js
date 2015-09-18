/**
 * Created by zano on 8/30/2015.
 */

var io=require('socket.io-client')
var request = require('request');
var srequest = require('sync-request');
var fs = require('fs');
//var mysql      = require('mysql');
//var connection = mysql.createConnection({
//    host     : 'localhost',
//    port: 8889,
//    user     : 'root',
//    password : 'root',
//    database : 'footbrain'
// });

//connection.connect();




var matchId=process.argv[2];
var matchGoalLiveId=process.argv[2];




matchId=fetchOriginalID(matchId);
getSocketIp(matchId);


function filterLive(matches){

    var tmp=[]
    for(var i in matches){
        var url=url='http://goallive.cdn.md/Mobile/Default.aspx?d={ib365e:1,t:1,APP:1011,lang:2,m:'+matches[i]+',ibe:0,T:7,iSe:1,tZ:-3,fMC:0}';
        var res=srequest('GET', url);
        console.log(matches[i]);
        var json=JSON.parse(res.getBody().toString());
        if(json.D) json.D.mRBI=="" || json.D.mRBI=="0" ?'':tmp.push(matches[i]);
    }

    return tmp;
}

function fetchOriginalID(matchId){

    url='http://goallive.cdn.md/Mobile/Default.aspx?d={ib365e:1,t:1,APP:1011,lang:2,m:'+matchId+',ibe:0,T:7,iSe:1,tZ:-3,fMC:0}';
    var res=srequest('GET', url);
    var json=JSON.parse(res.getBody().toString());
    return json.D.mGsmI;
}

function getSocketIp(matchId){

    var token=fetchToken(matchId);
    var url='http://visualisation.performgroup.com/animation/v2/index.html?token='+token;
    console.log(url);
    var res=srequest('GET', url);
    var html=res.getBody().toString();

    var matches=html.match(/http:\/\/((?:[0-9]{1,3}\.){3}[0-9]{1,3})/);
    //write(html);
    matches.length>0 && connectSocket(matches[0],token);

}

function write(data){
    fs.writeFile("tmp.txt", data.toString(), function(err) {
        if(err) {
            return console.log(err);
        }

        console.log("The file was saved!");
    });
}

function fetchToken(matchId){
    //var url='http://goal.cdn.md/FootballApi/Mobile/Default.aspx?d={"T":92,"tZ":-3,"rbi":'+matchId+',"APP":"1011","lang":2}';
   var url='http://visualisation.performgroup.com/getToken?customerId=goal&customerKey=fqtm39dkwipsn&userId=goal&gsmid='+matchId;

    console.log(url);
    var res=srequest('GET', url);
    return res.getBody().toString();
    console.log(res.getBody().toString());
    var json=JSON.parse(res.getBody().toString());
    return json.vt;
}

function connectSocket(ip,token){

    var socket = io(ip+"?token="+token+"&topreferer=visualisation.performgroup.com");//'52.19.165.22?token='+body+"&topreferer=visualisation.performgroup.com"

    socket.emit("subscribe", {
        Topic: matchId,
        LiveUpdates: "true",
        OldUpdates: "true",
        OddsUpdates: "true",
        VideoUpdates: "true",
        ConditionsUpdates: "true"
    });

    socket.on('connect',function(msg){
        console.log('connected');
    })
    socket.on('message',function(msg){
        //console.log(msg);
        console.log(msg);
        sendToSystem(msg.ActiveMQMessage);
    
    })


}

function sendToSystem(msg){

    var url = "http://104.236.73.160/listen/"+ encodeURIComponent(matchGoalLiveId) +"/"+ encodeURIComponent(JSON.stringify(msg));
    var res = srequest('GET', url);

}

function updateDB(data){

    fs.appendFile('message.txt',JSON.stringify(data), function (err) {
        console.log('data append');
    });

    //connection.query("INSERT INTO live_match_logs(match_id,data,updated_at,created_at) values('"+matchId+"','"+JSON.stringify(data)+"',NOW(),NOW())", function(err, rows, fields) {
    //    if (err) throw err;

    //});
}

console.log(matchId);