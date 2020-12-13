<?php

namespace Drupal\entity_plus\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

class EntityPopoverController extends ControllerBase {

  /**
   * Callback for ajax.
   */
  public function getPopoverOptions($entity_type, $entity_id, $target_entity_type_id, $target_bundle) {
    if (empty($entity_type) || empty($entity_id) || empty($target_entity_type_id) || empty($target_bundle)) {
      return JsonResponse::create([]);
    }

    $target_bundles = \Drupal::entityTypeManager()->getStorage($target_entity_type_id)->loadByProperties([
      'type' => $target_bundle,
    ]);

    $data = array_map(function ($item) {
      return $item->label();
    }, $target_bundles);

    return JsonResponse::create($data);
  }
}
