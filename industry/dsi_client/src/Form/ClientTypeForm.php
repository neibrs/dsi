<?php

namespace Drupal\dsi_client\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ClientTypeForm.
 */
class ClientTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $dsi_client_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $dsi_client_type->label(),
      '#description' => $this->t("Label for the Client type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $dsi_client_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\dsi_client\Entity\ClientType::load',
      ],
      '#disabled' => !$dsi_client_type->isNew(),
    ];

    if ($dsi_client_type->isNew()) {
      $options = [];
      foreach ($this->entityTypeManager->getDefinitions() as $entity_type) {
        // Only expose entities that have field UI enabled, only those can
        // get comment fields added in the UI.
        if ($entity_type->get('field_ui_base_route')) {
          $options[$entity_type->id()] = $entity_type->getLabel();
        }
      }
      $form['target_entity_type_id'] = [
        '#type' => 'select',
        '#default_value' => $dsi_client_type->getTargetEntityTypeId(),
        '#title' => t('Target entity type'),
        '#options' => $options,
        '#description' => t('The target entity type can not be changed after the comment type has been created.'),
      ];
    }
    else {
      $form['target_entity_type_id_display'] = [
        '#type' => 'item',
        '#markup' => $this->entityTypeManager->getDefinition($dsi_client_type->getTargetEntityTypeId())->getLabel(),
        '#title' => t('Target entity type'),
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $dsi_client_type = $this->entity;
    $status = $dsi_client_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Client type.', [
          '%label' => $dsi_client_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Client type.', [
          '%label' => $dsi_client_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($dsi_client_type->toUrl('collection'));
  }

}
