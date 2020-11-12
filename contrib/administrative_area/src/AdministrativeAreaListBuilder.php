<?php

namespace Drupal\administrative_area;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Administrative areas.
 *
 * @ingroup administrative_area
 */
class AdministrativeAreaListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Administrative area ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\administrative_area\Entity\AdministrativeArea $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.administrative_area.edit_form',
      ['administrative_area' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
