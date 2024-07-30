<?php

namespace Drupal\vwo\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\vwo\Service\SettingsService;

/**
 * VWO Settings form.
 */
class Settings extends ConfigFormBase {

  protected $settingsService;

  /**
   * Constructs a new Settings form object.
   *
   * @param \Drupal\vwo\Service\SettingsService $settings_service
   *   The settings service.
   */
  public function __construct(SettingsService $settings_service) {
    $this->settingsService = $settings_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('vwo.settings_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'vwo_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'vwo.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $settings = $this->settingsService->getSettings();

    $form['id_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Account'),
    ];
    $form['id_fieldset']['id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('VWO Account ID'),
      '#description' => $this->t('Your numeric Account ID or placeholder "NONE". This is the number after <q>var _vis_opt_account_id =</q> in the VWO Smart Code.'),
      '#size' => 15,
      '#maxlength' => 20,
      '#required' => TRUE,
      '#default_value' => ($settings['id'] == NULL) ? 'NONE' : $settings['id'],
    ];

    $form['synchtype_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Asynchronous/ Synchronous loading'),
    ];
    $form['synchtype_fieldset']['synchtype'] = [
      '#type' => 'radios',
      '#title' => $this->t('Javascript loading method'),
      '#default_value' => $settings['synchtype'],
      '#options' => [
        'async' => $this->t('Asynchronous'),
        'sync' => $this->t('Synchronous'),
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = [
      'id' => $form_state->getValue('id'),
      'synchtype' => $form_state->getValue('synchtype'),
    ];

    $this->settingsService->setSettings($values);
  }

}
