<?php

namespace Drupal\entity_plus\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\inline_entity_form\Form\EntityInlineForm as EntityInlineFormBase;

class EntityInlineForm extends EntityInlineFormBase {

  /**
   * {@inheritdoc}
   */
  public function entityForm(array $entity_form, FormStateInterface $form_state) {
    // 设置表单主题。参考 \Drupal\Core\Form\FormBuilder::prepareForm().
    $entity_form['#theme'] = $entity_form['#entity']->getEntityTypeId() . '_form';

    $entity_form = parent::entityForm($entity_form, $form_state);

    return $entity_form;
  }

}
