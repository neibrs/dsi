<?php

namespace Drupal\lookup;

interface LookupManagerInterface {

  /**
   * @return int
   */
  public function getLookupIdByName($type, $name);

}
