<?php

namespace Drupal\organization\Plugin\views\filter;

use Drupal\Core\Entity\Element\EntityAutocomplete;
use Drupal\Core\Form\FormStateInterface;
use Drupal\organization\Entity\Organization;
use Drupal\views\Plugin\views\filter\InOperator;

/**
 * Filter by term id.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("organization_subordinates")
 */
class OrganizationSubordinates extends InOperator {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['depth'] = ['default' => 0];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $parents = $this->value;

    if (empty($parents)) {
      return;
    }

    if (!is_array($parents)) {
      $parents = [$parents];
    }

    /** @var \Drupal\organization\OrganizationStorageInterface $organization_storage */
    $organization_storage = \Drupal::entityTypeManager()->getStorage('organization');
    $subordinates = [];
    foreach ($parents as $parent) {
      $subordinates += $organization_storage->loadAllChildren($parent, $subordinates);
    }

    $this->ensureMyTable();
    $this->query->addWhere($this->options['group'], "$this->tableAlias.$this->realField", array_keys($subordinates), $this->operator);
  }

  /**
   * {@inheritdoc}
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    $organizations = $this->value ? Organization::loadMultiple($this->value) : [];
    $default_value = EntityAutocomplete::getEntityLabels($organizations);
    $form['value'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Organization'),
      '#description' => $this->t('Enter a comma separated list of organization names.'),
      '#target_type' => 'organization',
      '#tags' => TRUE,
      '#default_value' => $default_value,
      '#process_default_value' => $this->isExposed(),
    ];

    $user_input = $form_state->getUserInput();
    $form_state->setUserInput($user_input);
  }

  /**
   * {@inheritdoc}
   */
  protected function valueValidate($form, FormStateInterface $form_state) {
    $ids = [];
    if ($values = $form_state->getValue(['options', 'value'])) {
      foreach ($values as $value) {
        $ids[] = $value['target_id'];
      }
      sort($ids);
    }
    $form_state->setValue(['options', 'value'], $ids);
  }

  protected function valueSubmit($form, FormStateInterface $form_state) {
    $this->valueOptions = $form_state->getValues()['options']['value'];
  }
}
