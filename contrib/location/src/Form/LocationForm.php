<?php

namespace Drupal\location\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_plus\Entity\ContentEntityForm;

/**
 * Form controller for Location edit forms.
 *
 * @ingroup location
 */
class LocationForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\location\Entity\Location */
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

    $form_state->setRedirect('entity.location.collection');
  }

}
