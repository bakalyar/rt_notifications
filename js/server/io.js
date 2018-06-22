var app = require('express')();
var server = require('http').Server(app);
var io = require('socket.io')(server);

server.listen(3000);

console.log('Server has started.');

io.on('connection', function(socket) {
  // If connected user is subscribed join him to the room.
  socket.on('subscribed users', function(data) {
    socket.join(data.user);
  });

  // Send message.
  socket.on('send message', function(data) {
    var uids = data.uids;
    delete data.uids;

    uids.map(function (uid) {
      // Send just to subscribed users.
      io.to(uid).emit('outcome message', data);

      // Logging.
      console.log('Message has sent to user ' + uid);
      console.log('Text: ' + data.text);
      console.log('Variables: ' + JSON.stringify(data.arguments));
    });
  });
});
