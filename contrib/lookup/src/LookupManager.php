<?php

namespace Drupal\lookup;

class LookupManager implements LookupManagerInterface {

  /**
   * {@inheritdoc}
   */
  public function getLookupIdByName($type, $name) {
    $ids = \Drupal::entityTypeManager()->getStorage('lookup')->getQuery()
      ->condition('type', $type)
      ->condition('name', $name)
      ->execute();

    return reset($ids);
  }

}
