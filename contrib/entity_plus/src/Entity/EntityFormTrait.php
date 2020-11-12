<?php

namespace Drupal\entity_plus\Entity;

use Drupal\Core\Form\FormStateInterface;

trait EntityFormTrait {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    // 统一处理待翻译字符串信息，避免需翻译的字符串过多.
    if ($bundle_entity_type = $entity->getEntityType()->getBundleEntityType()) {
      $type = \Drupal::entityTypeManager()->getStorage($bundle_entity_type)->load($entity->bundle())->label();
    }
    else {
      $type = $entity->getEntityType()->getLabel();
    }
    $context = ['@type' => $type, '%title' => $entity->label(), 'link' => $entity->toLink($this->t('View'))->toString()];
    $t_args = ['@type' => $type, '%title' => $entity->label()];
    switch ($status) {
      case SAVED_NEW:
        if ($bundle_entity_type) {
          $this->logger($entity->getEntityTypeId())->notice('@type: added %title.', $context);
        }
        else {
          $this->logger($entity->getEntityTypeId())->notice('added %title.', $context);
        }
        $this->messenger()->addMessage($this->t('@type %title has been created.', $t_args));
        break;

      default:
        if ($bundle_entity_type) {
          $this->logger($entity->getEntityTypeId())->notice('@type: updated %title.', $context);
        }
        else {
          $this->logger($entity->getEntityTypeId())->notice('updated %title.', $context);
        }
        $this->messenger()->addStatus($this->t('@type %title has been updated.', $t_args));
    }

    return $status;
  }

}
