<?php

namespace Drupal\person\Plugin\views\argument;

use Drupal\views\Plugin\views\argument\NumericArgument;

/**
 * Argument handler to accept a person.
 *
 * @ViewsArgument("person")
 */
class PersonArgument extends NumericArgument {

  /**
   * {@inheritdoc}
   */
  public function title() {
    if ($entity = \Drupal::entityTypeManager()->getStorage('person')->load($this->argument)) {
      return $entity->label();
    }

    return parent::title();
  }

}
