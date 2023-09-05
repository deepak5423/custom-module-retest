<?php

namespace Drupal\news_content_management\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Created a config for form api key.
 */
class ApiHeader extends ConfigFormBase {

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'api_header';
  }

  /**
   * {@inheritDoc}
   */
  public function getEditableConfigNames() {
    return [
      'form.settings',
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {    
    $data = $this->config('form.settings')->get('api_key');

    $form = [];
    $form['api_key'] = [
      '#type' => 'textfield',
      '#placeholder' => $this->t('Api Key'),
      '#default_value' => $data ?? '',
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(&$form, FormStateInterface $form_state) {
    $api_key = $form_state->getValue('api_key');
    $config = $this->config("form.settings");
    $config->set('api_key', $api_key);
    $config->save();
  }

}
