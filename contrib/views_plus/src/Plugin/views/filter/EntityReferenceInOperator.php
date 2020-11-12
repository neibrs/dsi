<?php

namespace Drupal\views_plus\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\OptGroup;
use Drupal\views\FieldAPIHandlerTrait;
use Drupal\views\Plugin\views\filter\InOperator;

/**
 * @ViewsFilter("entity_reference_in_operator")
 */
class EntityReferenceInOperator extends InOperator {

  use FieldAPIHandlerTrait;

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['properties']['default'] = [];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getValueOptions() {
    if (!isset($this->valueOptions)) {
      $options = \Drupal::service('plugin.manager.entity_reference_selection')
        ->getSelectionHandler($this->getFieldDefinition())
        ->getReferenceableEntities();
      $options = OptGroup::flattenOptions($options);

      $this->valueOptions = $options;
    }

    return $this->valueOptions;
  }

  public function valueForm(&$form, FormStateInterface $form_state) {
    parent::valueForm($form, $form_state);

    if (!empty($this->definition['widget_type'])) {
      switch ($this->definition['widget_type']) {
        case 'entity_autocomplete':
          $form['value']['#type'] = 'entity_autocomplete';
          $definition = $this->getFieldStorageDefinition();
          $form['value']['#target_type'] = $definition->getSetting('target_type');
          $form['value']['#tags'] = TRUE;
          $form['value']['#size'] = '100%';

          if ($handler_settings = $definition->getSetting('handler_settings')) {
            if (isset($handler_settings['target_bundles'])) {
              $form['value']['#selection_settings']['target_bundles'] = $handler_settings['target_bundles'];
            }
          }

          break;
      }
    }
  }

}
