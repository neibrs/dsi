<?php

namespace Drupal\person\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\entity_plus\Entity\ContentEntityForm;
use Drupal\views\Entity\View;

/**
 * Form controller for Person edit forms.
 *
 * @ingroup person
 */
class PersonForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $entity_types = \Drupal::entityTypeManager()->getStorage('person_type')->loadByProperties([
      'status' => true,
    ]);
    $types = [];
    foreach ($entity_types as $key => $entity_type) {
      $types[$key] = $entity_type->label();
    }
    $form['type']['widget']['#options'] = $types;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function actions(array $form, FormStateInterface $form_state) {
    $element = parent::actions($form, $form_state);

    $element['submit'] = [
      '#type' => 'submit',
      '#value' => '保存并返回',
      '#button_type' => 'primary',
      '#weight' => 20,
      '#submit' => ['::submitForm', '::save'],
    ];

    $element['keep_adding'] = [
      '#type' => 'submit',
      '#value' => '保存并继续添加',
      '#button_type' => 'primary',
      '#weight' => 20,
      '#submit' => ['::submitForm', '::save'],
    ];

    $element['add_other_info'] = [
      '#type' => 'submit',
      '#value' => '保存并完善其它内容',
      '#button_type' => 'primary',
      '#weight' => 20,
      '#submit' => ['::submitForm', '::save'],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;

    parent::save($form, $form_state);

    $type = reset($form_state->getValue('type'));
    $op = $form_state->getUserInput()['op'];
    switch ($op) {
      case '保存并继续添加':
        $options = [];
        $query = $this->getRequest()->query;
        if ($query->has('destination')) {
          $options['query']['destination'] = $query->get('destination');
          $query->remove('destination');
        }
        $form_state->setRedirect('entity.person.add_form', ['person_type' => $type['target_id']], $options);
        break;
      case '保存并完善其它内容':
        $options = [];
        $query = $this->getRequest()->query;
        if ($query->has('destination')) {
          $options['query']['destination'] = $query->get('destination');
          $query->remove('destination');
        }
        $form_state->setRedirect('entity.person.canonical', ['person' => $entity->id()], $options);
        break;
    }

  }

}
