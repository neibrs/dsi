<?php

namespace Drupal\responsibility;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Responsibility entities.
 *
 * @ingroup responsibility
 */
class ResponsibilityListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\responsibility\Entity\Responsibility $entity */
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.responsibility.edit_form',
      ['responsibility' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
