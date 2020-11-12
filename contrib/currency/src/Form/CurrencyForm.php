<?php

namespace Drupal\currency\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class CurrencyForm.
 */
class CurrencyForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $currency = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $currency->label(),
      '#description' => $this->t("Label for the Currency."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $currency->id(),
      '#machine_name' => [
        'exists' => '\Drupal\currency\Entity\Currency::load',
      ],
      '#disabled' => !$currency->isNew(),
    ];

    // TODO symbol field
    // TODO precision field

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $currency = $this->entity;
    $status = $currency->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Currency.', [
          '%label' => $currency->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Currency.', [
          '%label' => $currency->label(),
        ]));
    }
    $form_state->setRedirectUrl($currency->toUrl('collection'));
  }

}
