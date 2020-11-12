<?php

namespace Drupal\organization_hierarchy;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Organization hierarchy entities.
 *
 * @ingroup organization_hierarchy
 */
class OrganizationHierarchyListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Organization hierarchy ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\organization_hierarchy\Entity\OrganizationHierarchy */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.organization_hierarchy.edit_form',
      ['organization_hierarchy' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
