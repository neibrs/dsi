<?php

namespace Drupal\layout_template\Form;

use Drupal\Core\Entity\Display\EntityFormDisplayInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_plus\Entity\ContentEntityForm;
use Drupal\layout_template\Entity\LayoutTemplateInterface;

/**
 * Form controller for Layout template edit forms.
 *
 * @ingroup layout_template
 */
class LayoutTemplateForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\layout_template\Entity\LayoutTemplate */
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;

    switch ($entity->bundle()) {
      case 'entity_form_display':
        $this->buildEntityFormDisplayConfig($form, $form_state, $entity);
        break;
      case 'view':
        $this->buildViewConfig($form, $form_state, $entity);
        // TODO
    }

    // Polish style
    $form['#attributes']['class'][] = 'form-horizontal';
    $form['#theme_wrappers'] = ['form__box'];

    return $form;
  }

  public function buildEntityFormDisplayConfig(array &$form, FormStateInterface $form_state, LayoutTemplateInterface $entity) {
    /** @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface $entity_form_display */
    $entity_form_display = $entity->getRelatedConfig();
    $configuration = $entity->getConfiguration();

    $form['columns'] = [
      '#type' => 'select',
      '#title' => $this->t('Columns'),
      '#options' => [1 => '1', 2 => '2', 3 => '3', 4 => '4'],
      '#default_value' => isset($configuration['columns']) ? $configuration['columns'] : 3,
    ];

    $fields = $this->entityManager->getFieldDefinitions($entity_form_display->getTargetEntityTypeId(), $entity_form_display->getTargetBundle());
    $form['components'] = [
      '#type' => 'table',
      '#header' => [$this->t('Component'), $this->t('Visible'), $this->t('Columns')],
    ];
    foreach ($entity_form_display->getComponents() as $name => $component) {
      if (in_array($name, ['langcode'])) {
        continue;
      }
      $form['components'][$name]['component'] = [
        '#markup' => $fields[$name]->getLabel(),
      ];
      $form['components'][$name]['visible'] = [
        '#type' => 'checkbox',
        '#default_value' => isset($configuration['components'][$name]['visible']) ? $configuration['components'][$name]['visible'] : TRUE,
      ];
      $form['components'][$name]['columns'] = [
        '#type' => 'select',
        '#options' => [1 => '1', 2 => '2', 3 => '3', 4 => '4'],
        '#default_value' => isset($configuration['components'][$name]['columns']) ? $configuration['components'][$name]['columns'] : 1,
      ];
    }

    /** @var \Drupal\layout_template\LayoutTemplateManagerInterface $layout_template_manager */
    $layout_template_manager = \Drupal::service('layout_template.manager');
    foreach ($entity_form_display->getComponents() as $name => $component) {
      if ($component['type'] == 'inline_entity_form_complex') {
        $entity_type_id = $fields[$name]->getSetting('target_type');
        $form_mode = $component['settings']['form_mode'];
        $bundle_entity_type = $this->entityTypeManager->getDefinition($entity_type_id)->getBundleEntityType();
        if ($bundle_entity_type) {
          $bundle_entities = $this->entityTypeManager->getStorage($bundle_entity_type)->loadMultiple();
          foreach ($bundle_entities as $bundle => $entity) {
            $child_form_display = $layout_template_manager->getEntityFormDisplay($entity_type_id, $bundle, $form_mode);
            $form['entity_form_displays'][$child_form_display->id()] = $this->buildConfigForm($child_form_display, $configuration);
          }
        }
        else {
          $bundle = $entity_type_id;
        }
      }
    }

  }

  protected function buildConfigForm(EntityFormDisplayInterface $entity_form_display, $configuration) {
    $form = [];
    $form['columns'] = [
      '#type' => 'select',
      '#title' => $this->t('Columns'),
      '#options' => [1 => '1', 2 => '2', 3 => '3', 4 => '4'],
      '#default_value' => isset($configuration['columns']) ? $configuration['columns'] : 3,
    ];

    $fields = $this->entityManager->getFieldDefinitions($entity_form_display->getTargetEntityTypeId(), $entity_form_display->getTargetBundle());
    $form['components'] = [
      '#type' => 'table',
      '#header' => [$this->t('Component'), $this->t('Visible'), $this->t('Column span')],
    ];
    foreach ($entity_form_display->getComponents() as $name => $component) {
      if (in_array($name, ['langcode'])) {
        continue;
      }
      $form['components'][$name]['component'] = [
        '#markup' => $fields[$name]->getLabel(),
      ];
      $form['components'][$name]['visible'] = [
        '#type' => 'checkbox',
        '#default_value' => isset($configuration['components'][$name]['visible']) ? $configuration['components'][$name]['visible'] : TRUE,
      ];
      $form['components'][$name]['columns'] = [
        '#type' => 'select',
        '#options' => [1 => '1', 2 => '2', 3 => '3', 4 => '4'],
        '#default_value' => isset($configuration['components'][$name]['columns']) ? $configuration['components'][$name]['columns'] : 1,
      ];
    }

    return $form;
  }

  public function buildViewConfig(array &$form, FormStateInterface $form_state, LayoutTemplateInterface $entity) {
    /** @var \Drupal\views\ViewEntityInterface $view */
    $view = $entity->getRelatedConfig();
    $configuration = $entity->getConfiguration();
    $display = $view->getDisplay('default');

    $form['fields'] = [
      '#type' => 'table',
      '#caption' => $this->t('Data items setting'),
      '#header' => [$this->t('Field'), $this->t('Visible'), $this->t('Label')],
    ];
    foreach ($display['display_options']['fields'] as $key => $field) {
      $form['fields'][$key]['field'] = [
        '#markup' => $field['label'],
      ];
      $form['fields'][$key]['visible'] = [
        '#type' => 'checkbox',
        '#default_value' => isset($configuration['fields'][$key]['visible']) ? $configuration['fields'][$key]['visible'] : TRUE,
      ];
      $form['fields'][$key]['label'] = [
        '#type' => 'textfield',
        '#default_value' => isset($configuration['fields'][$key]['label']) ? $configuration['fields'][$key]['label'] : $field['label'],
      ];
    }

    $form['filters'] = [
      '#type' => 'table',
      '#caption' => $this->t('Filters setting'),
      '#header' => [$this->t('Filter'), $this->t('Visible')],
    ];
    foreach ($display['display_options']['filters'] as $key => $filter) {
      if ($filter['exposed']) {
        $form['filters'][$key]['filter'] = [
          '#markup' => $filter['expose']['label'],
        ];
        $form['filters'][$key]['visible'] = [
          '#type' => 'checkbox',
          '#default_value' => isset($configuration['filters'][$key]['visible']) ? $configuration['filters'][$key]['visible'] : TRUE,
        ];
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $configuration = [];
    switch ($entity->bundle()) {
      case 'entity_form_display':
        $configuration['columns'] = $form_state->getValue('columns');
        $configuration['components'] = $form_state->getValue('components');
        $configuration['entity_form_displays'] = $form['entity_form_displays'];
        //        $configuration['entity_form_displays'] = $form_state->getValue('entity_form_displays');
        break;
      case 'view':
        $configuration['fields'] = $form_state->getValue('fields');
        $configuration['filters'] = $form_state->getValue('filters');
        break;
    }

    $entity->set('configuration', $configuration);

    parent::save($form, $form_state);

  }

}
