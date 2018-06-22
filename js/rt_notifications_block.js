(function ($, Drupal) {
  var counter = 0;

  Drupal.behaviors.rtNotificationsBlockBehavior = {
    attach: function (context, settings) {
      $(context).find('.rt-notifications').once('rt-notifications').each(function(i, wrapper) {
        if (typeof settings.rtNotificationsServer !== 'undefined') {
          var socket = io(settings.rtNotificationsServer);
          var $counter_selector = $('#counter');
          var $list_selector = $('#list');

          // Clicking on a notification item mark it as read.
          var onUnreadItemClick = function(item) {
            var $item = $(item);

            if (counter > 0) {
              counter--;
              $counter_selector.text(counter);

              if (counter === 0) {
                $counter_selector.text('');
                $counter_selector.css('background-color', '');
              }
            }

            $item.removeClass('unread');
            $item.addClass('read');
          };

          // Add new message item.
          if (typeof settings.user.uid !== 'undefined') {
            var user_from_drupal = settings.user.uid;

            // Tell io-server that this user is subscribed for notifications.
            socket.emit('subscribed users', { user: user_from_drupal });

            // Get a new notification.
            socket.on('outcome message', function(data) {
              var msg = Drupal.t(data.text, data.arguments) +'&nbsp;<span class="date">' + data.date + '</span>';
              $list_selector.prepend('<p class="unread">' + msg + '</p>');
              counter++;
              $counter_selector.text(counter);
              $counter_selector.css('background-color', '#DB3B21');
              $list_selector.find('p.unread').once('rt-notifications-unread').each(function() {
                $(this).click(function () {
                  onUnreadItemClick(this);
                });
              });
            });
          }

          // Open/close notifications list.
          $('#button').click(function () {
            $list_selector.fadeToggle('fast', 'linear');
            return false;
          });
          $(document).click(function () {
            $list_selector.hide();
          });
          $list_selector.click(function () {
            return false;
          });
        }
      });
    }
  };
})(jQuery, Drupal);
