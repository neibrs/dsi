<?php

namespace Drupal\location\Plugin\EntityReferenceSelection;

use Drupal\entity_plus\Plugin\EntityReferenceSelection\DefaultSelection;

/**
 * @EntityReferenceSelection(
 *   id = "default:location",
 *   label = @Translation("Location selection"),
 *   entity_types = {"location"},
 *   group = "default",
 *   weight = 1
 * )
 */
class LocationSelection extends DefaultSelection {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'match_fields' => ['name', 'address', 'pinyin'],
    ] + parent::defaultConfiguration();
  }

}
