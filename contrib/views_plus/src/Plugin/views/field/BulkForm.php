<?php

namespace Drupal\views_plus\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\BulkForm as BulkFormBase;

/**
 * 提供批量修改功能.
 */
class BulkForm extends BulkFormBase {

  protected function getExtraOptions() {
    return [
      'bulk_update' => [
        'title' => $this->t('Bulk update'),
        'route_name' => 'entity_plus.entity_bulk_update_form',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function getBulkOptions($filtered = TRUE) {
    $options = parent::getBulkOptions($filtered);

    foreach ($this->getExtraOptions() as $key => $value) {
      $options[$key] = $value['title'];
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function viewsFormSubmit(&$form, FormStateInterface $form_state) {
    if ($form_state->get('step') == 'views_form_views_form') {
      $action = $form_state->getValue('action');
      $extra_options = $this->getExtraOptions();
      if (isset($extra_options[$action])) {
        $user_input = $form_state->getUserInput();
        $selected = array_filter($user_input[$this->options['id']]);
        $ids = [];
        foreach ($selected as $bulk_form_key) {
          $key = base64_decode($bulk_form_key);
          $key_parts = json_decode($key);

          if (count($key_parts) === 3) {
            $revision_id = array_pop($key_parts);
          }
          $ids[] = array_pop($key_parts);
        }

        $form_state->setRedirect($extra_options[$action]['route_name'], [
          'entity_type_id' => $this->definition['entity_type'],
          'entity_ids' => implode(',', $ids),
        ], [
          'query' => \Drupal::destination()->getAsArray(),
        ]);
        return;
      }
    }

    parent::viewsFormSubmit($form, $form_state);
  }

}
