<?php

namespace Drupal\eabax_core\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\inline_entity_form\Form\EntityInlineForm as EntityInlineFormBase;

class EntityInlineForm extends EntityInlineFormBase {

  /**
   * {@inheritdoc}
   */
  public function entityForm(array $entity_form, FormStateInterface $form_state) {
    $form = parent::entityForm($entity_form, $form_state);

    return [
      '#attributes' => [
        'class' => ['container-inline'],
      ],
    ] + $form;
  }

}
