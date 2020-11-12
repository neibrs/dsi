<?php

namespace Drupal\eabax_core\Entity;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\Core\TypedData\TranslatableInterface as TranslatableDataInterface;

class ConfigEntityViewBuilder extends EntityViewBuilder {

  protected function getBuildDefaults(EntityInterface $entity, $view_mode) {
    // Allow modules to change the view mode.
    $context = [];
    $this->moduleHandler()->alter('entity_view_mode', $view_mode, $entity, $context);

    $build = [
      "#{$this->entityTypeId}" => $entity,
      '#view_mode' => $view_mode,
      // Collect cache defaults for this entity.
      '#cache' => [
        'tags' => Cache::mergeTags($this->getCacheTags(), $entity->getCacheTags()),
        'contexts' => $entity->getCacheContexts(),
        'max-age' => $entity->getCacheMaxAge(),
      ],
    ];

    // Add the default #theme key if a template exists for it.
    if ($this->themeRegistry->getRuntime()->has($this->entityTypeId)) {
      $build['#theme'] = $this->entityTypeId;
    }

    // Cache the rendered output if permitted by the view mode and global entity
    // type configuration.
    if ($this->isViewModeCacheable($view_mode) && !$entity->isNew() && $this->entityType->isRenderCacheable()) {
      $build['#cache'] += [
        'keys' => [
          'entity_view',
          $this->entityTypeId,
          $entity->id(),
          $view_mode,
        ],
        'bin' => $this->cacheBin,
      ];

      if ($entity instanceof TranslatableDataInterface && count($entity->getTranslationLanguages()) > 1) {
        $build['#cache']['keys'][] = $entity->language()->getId();
      }
    }

    return $build;
  }

}
