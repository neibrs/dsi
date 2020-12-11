<?php

namespace Drupal\entity_plus\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class EntityPopoverForm extends FormBase {

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'entity_popover_form';
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $entity_type = NULL, $entity_id = NULL, $target_entity_type_id = NULL, $target_bundle = NULL) {

    // 忽略所有
    if (empty($entity_type) || empty($entity_id) || empty($target_entity_type_id) || empty($target_bundle)) {
      return $form;
    }

    $target_bundles = \Drupal::entityTypeManager()->getStorage($target_entity_type_id)->loadByProperties([
      'type' => $target_bundle,
    ]);

    $options = array_map(function ($item) {
      return $item->label();
    }, $target_bundles);

    $form['items'] = [
      '#type' => 'selecte',
      '#options' => $options,
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
  }

}
