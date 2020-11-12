<?php

namespace Drupal\Tests\location\Traits;

use Drupal\location\Entity\Location;

trait LocationTestTraits {
  /**
   * @param array $settings
   * @return \Drupal\location\Entity\LocationInterface
   */
  protected function createLocation(array $settings = []) {
    $settings += [
      'name' => $this->randomMachineName(),
    ];
    $location = Location::create($settings);
    $location->save();

    return $location;
  }

}