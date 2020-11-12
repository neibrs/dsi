<?php

namespace Drupal\dsi_classification\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ClassificationForm.
 */
class ClassificationForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $dsi_classification = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $dsi_classification->label(),
      '#description' => $this->t("Label for the Classification."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $dsi_classification->id(),
      '#machine_name' => [
        'exists' => '\Drupal\dsi_classification\Entity\Classification::load',
      ],
      '#disabled' => !$dsi_classification->isNew(),
    ];

    $options = $this->entityTypeManager->getDefinitions();
    $options = array_map(function ($definition) {
      return $definition->getLabel()->render();
    }, $options);
    $form['target_entity_type_id'] = [
      '#type' => 'select',
      '#options' => $options,
      '#title' => $this->t('Target entity type'),
      '#default_value' => $this->entity->getTargetEntityTypeId(),
      '#ajax' => [
        'callback' => '::targetEntityTypeSwitch',
      ],
    ];
    $form['target_entity_bundle_id'] = [
      '#type' => 'select',
      '#id' => 'edit-target-entity-bundle-id',
      '#title' => $this->t('Target entity Bundle'),
      '#default_value' => $this->entity->getTargetEntityBundleId(),
    ];
    if ($target_entity_type_id = $this->entity->getTargetEntityTypeId()) {
      $bundles = \Drupal::service('entity_plus.entity_type_manager')->getEntityBundlesByEntityTypeId($target_entity_type_id);
      $form['target_entity_bundle_id']['#options'] = $bundles;
    }

    $collections = $this->entity->getCollections();
    $form['collections'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Collections'),
      '#options' => [],
      '#default_value' => empty($collections) ? [] : $collections,
    ];
    $classification_types = $this->entityTypeManager->getStorage('dsi_classification_type')->loadMultiple();
    $classification_types = array_map(function ($classification_type) {
      return $classification_type->label();
    }, $classification_types);

    if (is_array($classification_types) && is_array($collections)) {
      $classification_types = array_intersect_key($classification_types, $this->entity->getCollections());
      $form['collections']['#options'] = $classification_types;
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $dsi_classification = $this->entity;
    $status = $dsi_classification->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Classification.', [
          '%label' => $dsi_classification->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Classification.', [
          '%label' => $dsi_classification->label(),
        ]));
    }
    $form_state->setRedirectUrl($dsi_classification->toUrl('collection'));
  }

  public function targetEntityTypeSwitch(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $entity_type_id = $form_state->getValue('target_entity_type_id');
    $bundles = \Drupal::service('entity_plus.entity_type_manager')->getEntityBundlesByEntityTypeId($entity_type_id);
    $form['target_entity_bundle_id'] = [
      '#type' => 'select',
      '#options' => $bundles,
      '#id' => 'edit-target-entity-bundle-id',
    ];

    $response->addCommand(new ReplaceCommand('[id^="edit-target-entity-bundle-id"]', $form['target_entity_bundle_id']));
    return $response;
  }

}
