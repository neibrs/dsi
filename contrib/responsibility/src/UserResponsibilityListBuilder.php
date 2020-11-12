<?php

namespace Drupal\responsibility;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of User responsibilities.
 *
 * @ingroup responsibility
 */
class UserResponsibilityListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('User responsibility ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\responsibility\Entity\UserResponsibility $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.user_responsibility.edit_form',
      ['user_responsibility' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
