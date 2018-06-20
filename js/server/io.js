var app = require('express')();
var server = require('http').Server(app);
var io = require('socket.io')(server);

server.listen(3000);

console.log('Server has started.');

io.on('connection', function(socket) {
  socket.on('subscribe to user', function(data) {
    socket.join(data.user);
  });

  socket.on('send message', function(data) {
    data.uids.map(function (uid) {
      io.to(uid).emit('outcome message', data);
      console.log('Message has sent to user ' + uid);
      console.log('Text: ' + data.text);
      console.log('Variables: ' + JSON.stringify(data.arguments));
    });
  });
});
