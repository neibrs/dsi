<?php

namespace Drupal\report\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_filter\Form\FiltersFormBase;

class ReportFiltersOverrideForm extends FiltersFormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $report = NULL) {
    /** @var \Drupal\report\Entity\ReportInterface $report */
    if (!is_object($report)) {
      $report = \Drupal::entityTypeManager()->getStorage('report')->load($report);
    }
    $this->entity = $report;

    $settings = $report->get('settings');
    $filters_override = $report->getFiltersOverride() ?: [];
    return parent::buildForm($form, $form_state, $settings['base_table'], $filters_override);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\report\Entity\ReportInterface $entity */
    $entity = $this->entity;
    $entity->setFiltersOverride($form_state->getValue('filters'));
  }

}
