/**
 * Node.js service to retrieve stats from PHP script
 * and persist to MongoDb collection...
 */

sys = require("sys");
test = require("assert");
var http = require('http'),
mongoLibPath = '../../../../lib/vendor/node-mongodb-native/lib/mongodb';
var Db = require(mongoLibPath).Db,
Connection = require(mongoLibPath).Connection,
Server = require(mongoLibPath).Server,
Collection = require(mongoLibPath).Collection,
// BSON = require('../lib/mongodb').BSONPure;
BSON = require(mongoLibPath).BSONNative;

var dbhost = '127.0.0.1';
var dbport = Connection.DEFAULT_PORT;

var monSvrs = ['server1','server2','server3'];
var delay = 6000;
sys.puts("Connecting to " + dbhost + ":" + dbport);
var db = new Db('wpm', new Server(dbhost, dbport, {}), {
  native_parser:true
});
db.open(function(err, client) {
  if(err) throw err;
  console.log(err);
  collection = new Collection(client, 'basic');
  run(collection);
});
 
 
function pollHosts(){
  for(var i = 0; i < monSvrs.length; i++){

    var hostname = monSvrs[i];
    var server1 = http.createClient(80, hostname);
    var request = server1.request('GET', '/',{
      'host': hostname
    });
    request.end();
    request.on('response', function (response) {
      response.setEncoding('utf8');
      response.on('data', function (chunk) {
        /**
           * expected response:
           * {"server":"host","serverTime":1302681191,"fileCreation":"0.000094","mathCalcs":"0.025043","fileWrite":"0.000289","fileDelete":"0.014258","total":"0.041699"}
           */
        
        var parsed = JSON.parse(chunk);
        
        if(parsed){
          collection.insert(parsed);
        }		
      });
    });
  }
}

function run(collection){
  pollHosts();
  setTimeout(run, delay, collection);
}
