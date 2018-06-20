Real-Time Notifications
================
The module for Drupal 8 which allows to get real-time notifications by users
who are subscribed on some events via WebSocket protocol.

Installation
============
1. In Drupal root directory run: 'composer require wisembly/elephant.io'
2. Install 'nodejs' package(if don't have).
3. Go to: <your_modules_directory>/rt_notifications/js/server
4. Run: 'npm install'
5. Run: 'node io.js'
6. Set configuration for Websockets server here: admin/config/system/realtime-notifications
7. Enable modules: rt_notifications
8. Place the block 'Real-Time Notifications' in some region ('Header' for example)
9. Subscribe for actions of another user in user profile.