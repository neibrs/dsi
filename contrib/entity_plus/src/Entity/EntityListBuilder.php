<?php

namespace Drupal\entity_plus\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder as EntityListBuilderBase;

class EntityListBuilder extends EntityListBuilderBase {

  /**
   * {@inheritdoc}
   */
  public function getOperations(EntityInterface $entity) {
    $operations = parent::getOperations($entity);

    foreach ($operations as $key => $operation) {
      $options = $operation['url']->getOptions();
      // Using path prefix language.
      unset($options['language']);
      $operations[$key]['url']->setOptions($options);
    }

    return $operations;
  }

}
