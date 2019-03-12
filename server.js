
var http - require('http').Server();
var io = require('socket.io')(http);
var Redis = require('socket.io');

var redis = new Redis();
redis.subscribe('new-action');
redis.on('message', function(channel, message) {
    console.log('Message recieved: '+message);
    console.log('Channel: '+channel);
    io.emit(channel + ':' +message.event), message.data);
});

http.listen(3000, function() {
    console.log('Listening port: 3000');
});
