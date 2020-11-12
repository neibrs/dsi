<?php

namespace Drupal\Tests\person\Traits;

use Drupal\person\Entity\Person;
use Drupal\person\Entity\PersonType;

trait PersonTestTrait {

  /**
   * @param array $settings
   *   (optional) An associative array of settings for the person, as used in
   *   entity_create().
   *
   * @return \Drupal\person\Entity\PersonInterface
   *   The created person entity.
   */
  protected function createPerson(array $settings = []) {
    $settings += [
      'type' => 'employee',
      'name' => $this->randomMachineName(),
    ];
    $entity = Person::create($settings);
    $entity->save();

    return $entity;
  }

  /**
   * @param array $settings
   * @return \Drupal\person\Entity\PersonType
   */
  protected function createPersonType(array $settings = []) {
    $settings += [
      'id' => $this->randomMachineName(),
      'label' => $this->randomMachineName(),
    ];
    $entity = PersonType::create($settings);
    $entity->save();

    return $entity;
  }

}
