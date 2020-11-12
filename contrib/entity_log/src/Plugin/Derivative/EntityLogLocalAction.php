<?php

namespace Drupal\entity_log\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Provides local action definitions for all entity.
 */
class EntityLogLocalAction extends DeriverBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $this->derivatives = parent::getDerivativeDefinitions($base_plugin_definition);


    foreach (\Drupal::entityTypeManager()->getDefinitions() as $entity_type_id => $entity_type) {
      if (!($entity_type instanceof ContentEntityTypeInterface)) {
        continue;
      }
      $this->derivatives['entity.entity_log.entity_log' . $entity_type_id] = [
        'route_name' => 'entity.entity_log.entity_log',
        'title' => $this->t('Change log'),
        'weight' => 30,
        'route_parameters' => [
          'entity_type_id' => $entity_type_id,
        ],
        'appears_on' => ["entity.$entity_type_id.canonical"],
        'class' => '\Drupal\entity_log\Plugin\Menu\LocalAction\AddDestination',
      ];
    }

    return $this->derivatives;
  }
}
