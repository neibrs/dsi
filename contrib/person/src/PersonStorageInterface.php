<?php

namespace Drupal\person;

use Drupal\Core\Entity\ContentEntityStorageInterface;

interface PersonStorageInterface extends ContentEntityStorageInterface {

  /**
   * @return \Drupal\person\Entity\PersonInterface
   */
  public function loadOrCreateByName($name, $settings = []);

}
