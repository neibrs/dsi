<?php

namespace Drupal\data_security;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Data securities.
 *
 * @ingroup data_security
 */
class DataSecurityListBuilder extends EntityListBuilder {

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
    /* @var \Drupal\data_security\Entity\DataSecurity $entity */
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.data_security.edit_form',
      ['data_security' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
