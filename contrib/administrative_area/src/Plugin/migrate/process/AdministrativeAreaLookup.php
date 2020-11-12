<?php

namespace Drupal\administrative_area\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * @MigrateProcessPlugin(
 *   id = "administrative_area_lookup"
 * )
 */
class AdministrativeAreaLookup extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $storage = \Drupal::entityTypeManager()->getStorage('administrative_area');
    $connection = \Drupal::database();
    $entities = $storage->loadByProperties(['name' => $value]);
    if ($entity = reset($entities)) {
      return $entity->id();
    }

    if (mb_strlen($value) >= 4) {
      $administrative_area = mb_substr($value, 0, 2);
      $thoroughfare = mb_substr($value, -2, 2);
      $ids = $storage->getQuery()
        ->condition('type', 'administrative_area')
        ->condition('name', $connection->escapeLike($administrative_area) . '%', 'LIKE')
        ->execute();
      if ($administrative_area_id = reset($ids)) {
        // 处理诸如"吉林长春"等数据.
        $ids = $storage->getQuery()
          ->condition('parent', $administrative_area_id)
          ->condition('name', '%' . $connection->escapeLike($thoroughfare) . '%', 'LIKE')
          ->execute();
        if ($id = reset($ids)) {
          return $id;
        }

        // 处理诸如"河北路南"等数据.
        $localities = $storage->getQuery()
          ->condition('parent', $administrative_area_id)
          ->execute();
        $ids = $storage->getQuery()
          ->condition('parent', $localities, 'IN')
          ->condition('name', '%' . $connection->escapeLike($thoroughfare) . '%', 'LIKE')
          ->execute();
        if ($id = reset($ids)) {
          return $id;
        }
      }
    }
  }

}
