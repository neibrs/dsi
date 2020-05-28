<?php

namespace Drupal\eabax_core\Form;

use Drupal\Core\Entity\EntityDeleteForm;
use Drupal\Core\Form\FormStateInterface;

class BundleDeleteForm extends EntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $entity_type = $this->entity->getEntityType();
    $num_entities = $this->entityTypeManager->getStorage($entity_type->getBundleOf())
      ->getQuery()
      ->condition('type', $this->entity->id())
      ->count()
      ->execute();
    if ($num_entities) {
      $bundle_of_entity = $this->entityTypeManager->getDefinition($entity_type->getBundleOf());
      $caption = '<p>' . $this->formatPlural($num_entities, '%type is used by 1 piece of %entity on your site. You can not remove this %entity type until you have removed all of the %type %entity.', '%type is used by @count pieces of %entity on your site. You may not remove %type until you have removed all of the %type %entity.', ['%type' => $this->entity->label(), '%entity' => $bundle_of_entity->getLabel()]) . '</p>';
      $form['#title'] = $this->getQuestion();
      $form['description'] = ['#markup' => $caption];
      return $form;
    }

    return parent::buildForm($form, $form_state);
  }

}
