<?php

namespace Drupal\views_plus\Form;

use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class EntityBulkUpdateForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'entity_bulk_update_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $entity_type_id = NULL, $entity_ids = NULL) {
    $this->entityTypeManager = \Drupal::entityTypeManager();

    $this->entity_type_id = $entity_type_id;

    $entity_ids = explode(',', $entity_ids);
    $this->entity_ids = $entity_ids;

    $entities = $this->entityTypeManager->getStorage($entity_type_id)->loadMultiple($entity_ids);
    $bundle = current($entities)->bundle();
    $bundle_key = current($entities)->getEntityType()->getKey('bundle');

    $entities = array_map(function ($item) {
      return $item->label();
    }, $entities);
    if (count($entities) > 3) {
      $entities = array_slice($entities, 0, 3);
      $entities[] = '……等 ' . count($entity_ids) . ' 项';
    }
    $form['entities'] = [
      '#markup' => $this->t('<p>Bulk edit: @entities.</p>', ['@entities' => implode(', ', $entities)]),
    ];

    // Build widgets from form display.
    if ($bundle) {
      $this->entity = $this->entityTypeManager->getStorage($entity_type_id)->create([
          $bundle_key => $bundle,
        ]);
    }
    else {
      $this->entity = $this->entityTypeManager->getStorage($entity_type_id)->create();
    }
    $display = EntityFormDisplay::collectRenderDisplay($this->entity, 'default');
    foreach ($display->getComponents() as $key => $component) {
      if (in_array($key, ['name', 'attachments', 'langcode', 'person'])) {
        $display->removeComponent($key);
        continue;
      }
    }
    $display->buildForm($this->entity, $form, $form_state);
    $this->form_display = $display;

    $header = [
      $this->t('Item'),
      $this->t('Update'),
      $this->t('Value'),
    ];
    $form['fields'] = [
      '#type' => 'table',
      '#header' => $header,
      '#sticky' => TRUE,
      '#attributes' => [
        'class' => ['bulk-update'],
      ],
    ];

    $definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions($entity_type_id, $bundle ?: $entity_type_id);

    // 按照 weight 升序排序
    $components = [];
    foreach ($display->getComponents() as $key => $component) {
      $components[$key] = $component['weight'];
    }
    asort($components);

    foreach ($components as $key => $component) {
      $form['fields'][$key]['item'] = [
        '#markup' => $definitions[$key]->getLabel(),
      ];

      // Remove widget title.
      unset($form[$key]['widget']['#title']);
      unset($form[$key]['widget'][0]['#title']);
      unset($form[$key]['widget'][0]['value']['#title']);
      unset($form[$key]['widget'][0]['end_value']['#title']);
      unset($form[$key]['widget'][0]['target_id']['#title']);

      $form['fields'][$key]['update'] = [
        '#type' => 'checkbox',
      ];

      $form['fields'][$key]['value'] = $form[$key];
      unset($form[$key]);

    }

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Bulk update'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->form_display->extractFormValues($this->entity, $form, $form_state);

    $storage = $this->entityTypeManager->getStorage($this->entity_type_id);

    $entities = $storage->loadMultiple($this->entity_ids);
    foreach ($entities as $entity) {
      foreach ($form_state->getValue('fields') as $key => $value) {
        if ($value['update']) {
          $entity->$key = $this->entity->$key;
        }
      }
      $entity->save();
    }

    $this->messenger()->addStatus($this->t('Bulk update has been finished.'));
  }

}
