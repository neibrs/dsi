<?php

namespace Drupal\dsi_classification;

interface ClassificationRepositoryInterface {

  /**
   * @param null $entity_type_id
   *
   * @return \Drupal\dsi_classification\Entity\ClassificationInterface[]
   */
  public function getClassificationAllowedValues($entity_type_id = NULL);

}
