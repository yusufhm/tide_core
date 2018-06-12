<?php

namespace Drupal\tide_monsido\Form;

/**
 * @file
 * Tide Monsido Admin form.
 */

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Monsido settings for this site.
 */
class TideMonsidoAdminSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'tide_monsido_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['tide_monsido.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $settings = tide_monsido_get_settings();

    $form['enable_monsido'] = [
      '#default_value' => $settings['enable_monsido'],
      '#title' => $this->t('Enable Monsido integration'),
      '#type' => 'checkbox',
    ];

    $form['domain_token'] = [
      '#default_value' => $settings['domain_token'],
      '#description' => $this->t("Your Monsido Domain Token can be found in your tracking code on the line <code>_monsido.push(['_setDomainToken', <strong>'XYZ'</strong>]);</code> where <code><strong>XYZ</strong></code> is your Domain Token."),
      '#required' => TRUE,
      '#size' => 15,
      '#title' => $this->t('Monsido Domain Token'),
      '#type' => 'textfield',
    ];

    $form['enable_statistics'] = [
      '#default_value' => $settings['enable_statistics'],
      '#title' => $this->t('Enable Statistics'),
      '#type' => 'checkbox',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Trim some text values.
    $form_state->setValue('domain_token', trim($form_state->getValue('domain_token')));
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('tide_monsido.settings');
    $config
      ->set('enable_monsido', $form_state->getValue('enable_monsido'))
      ->set('domain_token', $form_state->getValue('domain_token'))
      ->set('enable_statistics', $form_state->getValue('enable_statistics'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
