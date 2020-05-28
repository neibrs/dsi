<?php

namespace Drupal\layout_template\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class LayoutTemplateTypeForm.
 */
class LayoutTemplateTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $layout_template_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $layout_template_type->label(),
      '#description' => $this->t("Label for the Layout template type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $layout_template_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\layout_template\Entity\LayoutTemplateType::load',
      ],
      '#disabled' => !$layout_template_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $layout_template_type = $this->entity;
    $status = $layout_template_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Layout template type.', [
          '%label' => $layout_template_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Layout template type.', [
          '%label' => $layout_template_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($layout_template_type->toUrl('collection'));
  }

}
