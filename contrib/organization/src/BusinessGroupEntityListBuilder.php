<?php

namespace Drupal\organization;

use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\entity_plus\Entity\EntityListBuilder;

class BusinessGroupEntityListBuilder extends EntityListBuilder {

  protected function alterQuery(QueryInterface $query) {
    // Add business group condition
    $query->addTag('business_group_access');
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntityIds() {
    $query = $this->getStorage()->getQuery()
      ->sort($this->entityType->getKey('id'));

    // Only add the pager if a limit is specified.
    if ($this->limit) {
      $query->pager($this->limit);
    }

    // Provide a change to alter query.
    $this->alterQuery($query);

    return $query->execute();
  }

}
