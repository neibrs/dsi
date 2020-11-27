<?php

namespace Drupal\alert\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_plus\Entity\ContentEntityForm;

/**
 * Form controller for Alert edit forms.
 *
 * @ingroup alert
 */
class AlertForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\alert\Entity\Alert */
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;

    parent::save($form, $form_state);

    $form_state->setRedirect('entity.alert.canonical', ['alert' => $entity->id()]);
  }

}
