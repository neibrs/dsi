<?php

namespace Drupal\alert;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Defines a class to build a listing of alerts.
 *
 * @ingroup alert
 */
class AlertListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Alert ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\alert\Entity\Alert */
    $row['id'] = $entity->id();
    $row['name'] = $entity->toLink();
    return $row + parent::buildRow($entity);
  }

}
