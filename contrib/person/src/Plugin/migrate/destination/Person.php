<?php

namespace Drupal\person\Plugin\migrate\destination;

use Drupal\import\Plugin\migrate\destination\EntityContentBase;
use Drupal\migrate\Row;

/**
 * @MigrateDestination(
 *   id = "entity:person",
 * )
 */
class Person extends EntityContentBase {

  /**
   * {@inheritdoc}
   */
  protected function getEntityId(Row $row) {
    if ($entity_id = parent::getEntityId($row)) {
      return $entity_id;
    }

    $storage = \Drupal::entityTypeManager()->getStorage('person');
    $number = $row->getDestinationProperty('number');
    if (!empty($number)) {
      $ids = $storage->getQuery()
        ->condition('number', $number)
        ->execute();
      return reset($ids);
    }

    $name = $row->getDestinationProperty('name');
    if (!empty($name)) {
      $ids = $storage->getQuery()
        ->condition('name', $name)
        ->execute();
      return reset($ids);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function import(Row $row, array $old_destination_id_values = []) {
    $lookup_storage = \Drupal::entityTypeManager()->getStorage('lookup');

    // 导入各种地址、电话、邮箱等.
    $personal_informations = [
      'address' => [
        'lookup_type' => 'person_address_type',
        'entity_type_id' => 'person_address',
      ],
      'phone' => [
        'lookup_type' => 'person_phone_type',
        'entity_type_id' => 'person_phone',
      ],
      'email' => [
        'lookup_type' => 'person_email_type',
        'entity_type_id' => 'person_email',
      ],
      'identification_information' => [
        'lookup_type' => 'identification_information_type',
        'entity_type_id' => 'identification_information',
      ],
    ];
    foreach ($personal_informations as $field_name => $personal_information) {
      $lookup_type = $personal_information['lookup_type'];
      $entity_type_id = $personal_information['entity_type_id'];

      $types = $lookup_storage->loadByProperties(['type' => $lookup_type]);
      $storage = \Drupal::entityTypeManager()->getStorage($entity_type_id);
      $ids = [];
      foreach ($types as $id => $type) {
        $column_name = $type->label();
        if ($value = $row->getSourceProperty($column_name)) {
          $flag = false;
          if (reset($old_destination_id_values)) {
            $entities = $storage->loadByProperties([
              'type' => $id,
              'person' => reset($old_destination_id_values),
            ]);
            if (!empty($entities)) {
              $entity = reset($entities);
              $entity->set('name', $value);
              $entity->save();
              $flag = true;
            }
          }
          if (!$flag) {
            $entity = $storage->create([
              'type' => $id,
              'name' => $value,
            ]);
            $entity->save();
            $ids[] = $entity->id();
          }
        }
      }
      // 找出所有
      if (!empty($ids)) {
        if (reset($old_destination_id_values)) {
          $entities = $storage->loadByProperties([
            'person' => reset($old_destination_id_values),
          ]);
          foreach ($entities as $entity) {
            $ids[] = $entity->id();
          }
        }
        $row->setDestinationProperty($field_name, $ids);
      }
    }

    return parent::import($row, $old_destination_id_values);
  }

}
