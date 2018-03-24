<?php

namespace Drupal\sigfox_migrate\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class MigrationConfigForm.
 */
class MigrationConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'migration_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('migrate_plus.migration.sigfox_location');

    $form['migration_source_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Migration source URL'),
      '#description' => $this->t('URL pointing to JSON endpoint. Basic authentication might be included in URL. Example: <em>https://USERNAME:PASSWORD@backend.sigfox.com/api/devices/DEVICEID/messages</em>'),
      '#maxlength' => 512,
      '#size' => 128,
      '#default_value' => $config->get('source.urls'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('migrate_plus.migration.sigfox_location')
      ->set('source.urls', $form_state->getValue('migration_source_url'))
      ->save();
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'migrate_plus.migration.sigfox_location',
    ];
  }

}
