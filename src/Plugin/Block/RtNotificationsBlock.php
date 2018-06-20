<?php

namespace Drupal\rt_notifications\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a 'Real-Time Notifications' Block.
 *
 * @Block(
 *   id = "rt_notifications_block",
 *   admin_label = @Translation("Real-Time Notifications"),
 *   category = @Translation("Real-Time Notifications"),
 * )
 */
class RtNotificationsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The current user service.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory, AccountInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->configFactory = $config_factory;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $list = [];
    $unread_count = 0;
    $build['notifications'] = [
      '#theme' => 'rt_notifications_block',
      '#notifications' => $list,
      '#count' => $unread_count,
    ];

    $websocket_server_config = $this->configFactory->get('rtn.websocket');
    $host = $websocket_server_config->get('host');
    $port = $websocket_server_config->get('port');
    $uri = '//' . $host . ':' . $port;
    $build['#attached']['drupalSettings']['rtNotificationsServer'] = $uri;
    $build['#attached']['library'][] = 'rt_notifications/rt_notifications_block';

    return $build;
  }

}
