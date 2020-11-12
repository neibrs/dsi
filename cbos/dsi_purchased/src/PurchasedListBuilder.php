<?php

namespace Drupal\dsi_purchased;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Purchased entities.
 *
 * @ingroup dsi_purchased
 */
class PurchasedListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Purchased ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\dsi_purchased\Entity\Purchased $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.dsi_purchased.edit_form',
      ['dsi_purchased' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
