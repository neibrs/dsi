<?php

namespace Drupal\entity_plus\Entity;

use Drupal\Core\Entity\ContentEntityConfirmFormBase as ContentEntityConfirmFormBaseBase;
use Drupal\Core\Form\FormStateInterface;

abstract class ContentEntityConfirmFormBase extends ContentEntityConfirmFormBaseBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $form['entity'] = \Drupal::entityTypeManager()
      ->getViewBuilder($this->entity->getEntityTypeId())
      ->view($this->entity, 'teaser');

    return $form;
  }

}
