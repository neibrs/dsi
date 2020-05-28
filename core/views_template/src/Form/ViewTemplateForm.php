<?php

namespace Drupal\views_template\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ViewTemplateForm.
 */
class ViewTemplateForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $view_template = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $view_template->label(),
      '#description' => $this->t("Label for the View template."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $view_template->id(),
      '#machine_name' => [
        'exists' => '\Drupal\views_template\Entity\ViewTemplate::load',
      ],
      '#disabled' => !$view_template->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $view_template = $this->entity;
    $status = $view_template->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label View template.', [
          '%label' => $view_template->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label View template.', [
          '%label' => $view_template->label(),
        ]));
    }
    $form_state->setRedirectUrl($view_template->toUrl('collection'));
  }

}
