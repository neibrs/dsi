<?php

namespace Drupal\entity_filter\Plugin\Field\FieldType;

interface EntityFilterItemInterface {

  /**
   * @return string
   */
  public function getTargetType();

}