<?php

namespace Drupal\rt_notifications\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\rt_notifications\Connector\WebSocketConnector;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;

/**
 * Configure settings for WebSocket server.
 *
 * @internal
 */
class WebSocketSettingsForm extends ConfigFormBase {

  /**
   * WebSocket connector.
   *
   * @var WebSocketConnector
   */
  protected $webSocketConnector;

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory, WebSocketConnector $websocket_connector) {
    parent::__construct($config_factory);

    $this->webSocketConnector = $websocket_connector;
}

/**
 * {@inheritdoc}
 */
public static function create(ContainerInterface $container) {
  return new static(
    $container->get('config.factory'),
    $container->get('rtn.websocket_client')
  );
}

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rt_notifications_websocket_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['rtn.websocket'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('rtn.websocket');
    if ($config->get('host') && $config->get('port')) {
      $this->webSocketConnector->connect(TRUE);
    }

    $form['host'] = [
      '#type' => 'textfield',
      '#title' => t('WebSocket host'),
      '#default_value' => $config->get('host') ? $config->get('host') : '127.0.0.1',
      '#required' => TRUE,
      '#maxlength' => 255,
      '#description' => '',
    ];
    $form['port'] = [
      '#type' => 'number',
      '#title' => t('WebSocket port'),
      '#default_value' => $config->get('port') ? $config->get('port') : 3000,
      '#required' => TRUE,
      '#maxlength' => 5,
      '#description' => '',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('rtn.websocket')
      ->set('host', $form_state->getValue('host'))
      ->set('port', $form_state->getValue('port'));

    $config->save();
    $this->webSocketConnector->connect(TRUE);

    parent::submitForm($form, $form_state);
  }

}
