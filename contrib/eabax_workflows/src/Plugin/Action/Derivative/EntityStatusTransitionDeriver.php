<?php

namespace Drupal\eabax_workflows\Plugin\Action\Derivative;

use Drupal\Core\Action\Plugin\Action\Derivative\EntityActionDeriverBase;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * @see \Drupal\eabax_workflows\Plugin\Action\WorkflowStatusTransition
 */
class EntityStatusTransitionDeriver extends EntityActionDeriverBase {

  public function getDerivativeDefinitions($base_plugin_definition) {
    if (empty($this->derivatives)) {
      $definitions = [];
      foreach ($this->getApplicableEntityTypes() as $entity_type_id => $entity_type) {
        $definition = $base_plugin_definition;
        $definition['type'] = $entity_type_id;
        $definition['label'] = $this->t('Transition @entity_type status', ['@entity_type' => $entity_type->getSingularLabel()]);
        $definitions[$entity_type_id] = $definition;
      }
      $this->derivatives = $definitions;
    }

    return $this->derivatives;
  }

  /**
   * {@inheritdoc}
   */
  protected function isApplicable(EntityTypeInterface $entity_type) {
    if ($entity_type instanceof ContentEntityTypeInterface) {
      /** @var \Drupal\Core\Field\FieldDefinitionInterface[] $fields */
      $fields = \Drupal::service('entity_field.manager')->getBaseFieldDefinitions($entity_type->id());
      foreach ($fields as $field) {
        if ($field->getType() == 'entity_status') {
          return TRUE;
        }
      }
    }

    return FALSE;
  }

}
