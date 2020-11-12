<?php

namespace Drupal\entity_plus\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Provides local action definitions for all entity bundles.
 */
class EntityLocalAction extends DeriverBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $this->derivatives = [];

    foreach (\Drupal::entityTypeManager()->getDefinitions() as $entity_type_id => $entity_type) {
      $appears_on = [];
      if ($entity_type->hasLinkTemplate('edit-form')) {
        $appears_on[] = "entity.$entity_type_id.edit_form";
      }
      if ($entity_type->hasLinkTemplate('canonical')) {
        $appears_on[] = "entity.$entity_type_id.canonical";
      }
      if (!empty($appears_on)) {
        if ($entity_type->hasLinkTemplate('collection')) {
          $this->derivatives["entity.$entity_type_id.collection"] = [
            'route_name' => "entity.$entity_type_id.collection",
            'title' => $this->t('List'),
            'appears_on' => ["entity.$entity_type_id.edit_form"],
          ];
        }
      }
    }

    foreach ($this->derivatives as &$entry) {
      $entry += $base_plugin_definition;
    }

    return $this->derivatives;
  }

}
