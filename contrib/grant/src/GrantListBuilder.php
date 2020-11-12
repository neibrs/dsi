<?php

namespace Drupal\grant;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Grants.
 *
 * @ingroup grant
 */
class GrantListBuilder extends EntityListBuilder {

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
    /* @var \Drupal\grant\Entity\Grant $entity */
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.grant.edit_form',
      ['grant' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
