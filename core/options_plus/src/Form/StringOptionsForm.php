<?php

namespace Drupal\options_plus\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class StringOptionsForm.
 */
class StringOptionsForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $entity = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $entity->label(),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#machine_name' => [
        'exists' => '\Drupal\options_plus\Entity\StringOptions::load',
      ],
      '#disabled' => !$entity->isNew(),
    ];

    $form['allowed_values'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Allow values list'),
      '#default_value' => implode("\n", $entity->getAllowedValues()),
      '#rows' => 10,
      '#element_validate' => [[get_class($this), 'validateAllowedValues']],
      '#description' => $this->t('Enter one value per line.')
    ];

    return $form;
  }

  /**
   * #element_validate callback for options field allowed values.
   */
  public static function validateAllowedValues($element, FormStateInterface $form_state) {
    $list = explode("\n", $element['#value']);
    $list = array_map('trim', $list);
    $list = array_filter($list, 'strlen');

    $form_state->setValueForElement($element, $list);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $string_options = $this->entity;
    $status = $string_options->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label String options.', [
          '%label' => $string_options->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label String options.', [
          '%label' => $string_options->label(),
        ]));
    }
    $form_state->setRedirectUrl($string_options->toUrl('collection'));
  }

}
