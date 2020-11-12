<?php

namespace Drupal\report\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_filter\Form\FiltersFormBase;

/**
 * Used by route entity.report.filters_form, which is used by columns/rows/filters editing.
 *
 * @see \Drupal\report\Plugin\ReportPluginBase
 * @see \Drupal\report\Plugin\Report\SimpleChart
 * @see \Drupal\report\Plugin\Report\CrossTable
 */
class ReportFiltersForm extends FiltersFormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $report = NULL, $setting_name = NULL) {
    /** @var \Drupal\report\Entity\ReportInterface $report */
    if (!is_object($report)) {
      $report = \Drupal::entityTypeManager()->getStorage('report')->load($report);
    }
    $this->entity = $report;
    $this->setting_name = $setting_name;

    $settings = $report->get('settings');
    $form = parent::buildForm($form, $form_state, $settings['base_table'], $settings[$setting_name]);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\report\Entity\ReportInterface $entity */
    $entity = $this->entity;
    $setting_name = $this->setting_name;

    $settings = $entity->get('settings');
    $settings[$setting_name] = $form_state->getValue('filters');
    $entity->set('settings', $settings);
    $entity->save();
  }
}
