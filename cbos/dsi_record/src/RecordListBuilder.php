<?php

namespace Drupal\dsi_record;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Record entities.
 *
 * @ingroup dsi_record
 */
class RecordListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Record ID');
    $header['name'] = $this->t('Name', [], ['context' => 'Record']);
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\dsi_record\Entity\Record $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.dsi_record.edit_form',
      ['dsi_record' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
