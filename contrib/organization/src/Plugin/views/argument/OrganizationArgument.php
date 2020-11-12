<?php

namespace Drupal\organization\Plugin\views\argument;

use Drupal\views\Plugin\views\argument\NumericArgument;

/**
 * Argument handler to accept a organization.
 *
 * @ViewsArgument("organization")
 */
class OrganizationArgument extends NumericArgument {

  /**
   * {@inheritdoc}
   */
  public function title() {
    if ($entity = \Drupal::entityTypeManager()->getStorage('organization')->load($this->argument)) {
      return $entity->label();
    }

    return parent::title();
  }

}
