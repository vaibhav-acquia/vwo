<?php

namespace Drupal\vwo\Service;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Class SettingsService
 *
 * Service to handle VWO settings.
 */
class SettingsService {

  protected $configFactory;

  /**
   * Constructs a new SettingsService object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * Gets the VWO settings.
   *
   * @return array
   *   An array of VWO settings.
   */
  public function getSettings() {
    $config = $this->configFactory->get('vwo.settings');
    return [
      'id' => $config->get('id'),
      'synchtype' => $config->get('synchtype'),
    ];
  }

  /**
   * Sets the VWO settings.
   *
   * @param array $values
   *   An array of values to set.
   */
  public function setSettings(array $values) {
    $config = $this->configFactory->getEditable('vwo.settings');
    $config->set('id', $values['id'])
           ->set('synchtype', $values['synchtype'])
           ->save();
  }

}
