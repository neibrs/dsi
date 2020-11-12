<?php

namespace Drupal\lookup;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Link;
use Drupal\entity_plus\Entity\EntityListBuilder;

/**
 * Defines a class to build a listing of Lookup entities.
 *
 * @ingroup lookup
 */
class LookupListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  protected function getEntityIds() {
    $query = $this->getStorage()->getQuery()
      ->sort($this->entityType->getKey('id'));

    if ($lookup_type = \Drupal::request()->get('lookup_type')) {
      $query->condition('type', $lookup_type);
    }

    // Only add the pager if a limit is specified.
    if ($this->limit) {
      $query->pager($this->limit);
    }
    return $query->execute();
  }

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
    /* @var \Drupal\lookup\Entity\Lookup $entity */
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.lookup.edit_form',
      ['lookup' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
