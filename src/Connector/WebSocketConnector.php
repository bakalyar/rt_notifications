<?php

namespace Drupal\rt_notifications\Connector;

use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version2X;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 *  Provides functionality for dialog with WebSocket server.
 */
class WebSocketConnector {

  /**
   * WebSocket client.
   *
   * @var \ElephantIO\Client
   */
  protected $client;

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The logger channel factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $logger;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a new WebSocketClient object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
   *   The logger channel factory.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, LoggerChannelFactoryInterface $logger, MessengerInterface $messenger) {
    $this->logger = $logger;
    $this->configFactory = $config_factory;
    $this->messenger = $messenger;
  }

  /**
   * Set connection with server.
   *
   * @param bool $status_message
   *   Status massage.
   */
  public function connect($status_message = FALSE) {
    $websocket_server_config = $this->configFactory->get('rtn.websocket');
    $host = $websocket_server_config->get('host');
    $port = $websocket_server_config->get('port');
    $uri = 'http://' . $host . ':' . $port;

    try {
      $socket_dialog = new Version2X($uri);
      $this->client = new Client($socket_dialog);
      $this->client->initialize();
      $msg = 'Connected successfully!';

      if ($status_message) {
        $this->messenger->addMessage($msg);
      }
    }
    catch (\Exception $exception) {
      $this->client = NULL;
      $msg = $exception->getMessage() . '. ';
      $msg .= 'Please check host and port of WebSocket server settings.';
      $this->logger->get('rt_notifications')->error($msg);

      if ($status_message) {
        $this->messenger->addMessage($msg, 'error');
      }
    }
  }

  /**
   * Get WebSocket (Elephant) client.
   */
  public function getClient() {
    return $this->client;
  }

  /**
   * Send message.
   */
  public function sendMessage(array $uids, $text, array $arguments = []) {
    $date = date('\a\t H:m:s \o\n l');
    $params = [
      'uids' => $uids,
      'text' => $text,
      'date' => $date,
      'arguments' => $arguments,
    ];
    $this->client->emit('send message', $params);
  }

  /**
   * Disconnect from server.
   */
  public function disconnect() {
    $this->client->close();
  }

}