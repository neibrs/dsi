<?php

namespace Drupal\organization_hierarchy\Plugin\migrate\destination;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\import\Plugin\migrate\destination\EntityContentBase;

/**
 * @MigrateDestination(
 *   id = "entity:organization"
 * )
 */
class EntityOrganization extends EntityContentBase {

  /**
   * {@inheritdoc}
   */
  protected function save(ContentEntityInterface $entity, array $old_destination_id_values = []) {
    if (!$entity->getParent() && ($entity->get('business_group')->target_id != $entity->id())) {
      $entity->set('parent', $entity->get('business_group')->target_id);
    }
    /** @var \Drupal\organization\Entity\OrganizationInterface $ids */
    $ids = parent::save($entity, $old_destination_id_values);

    if ($entity->parent->target_id) {
      $organization_hierarchy = \Drupal::entityTypeManager()->getStorage('organization_hierarchy')
        ->loadOrCreateActiveHierarchy($entity->parent->target_id);
  
      $found = FALSE;
      $subordinates = $organization_hierarchy->subordinates->referencedEntities();
      foreach ($subordinates as $subordinate) {
        if ($subordinate->id() == $entity->id()) {
          $found = TRUE;
          break;
        }
      }
      if (!$found) {
        $organization_hierarchy->subordinates->appendItem($entity);
        $organization_hierarchy->save();
      }
    }

    return $ids;
  }

}
