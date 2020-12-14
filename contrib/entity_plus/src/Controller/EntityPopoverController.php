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

    if (\Drupal::request()->getRealMethod() == "POST") {
      $data = \Drupal::request()->request->all();

      if (!empty($data['entity_field']) && !empty($data['id'])) {
        $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($entity_id);
        $entity_field_value = $entity->get($data['entity_field'])->target_id;

        if ($entity_field_value != $data['id']) {
          $entity->set($data['entity_field'], $data['id']);
          $entity->save();

          return JsonResponse::create(['success']);
        }
      }
      else {
        return JsonResponse::create(['Data is error!']);
      }
    }

    $target_bundles = \Drupal::entityTypeManager()->getStorage($target_entity_type_id)->loadByProperties([
      'type' => $target_bundle,
    ]);
    if (\Drupal::request()->getRealMethod() == "GET") {
      $data = array_map(function ($item) {
        return $item->label();
      }, $target_bundles);

      return JsonResponse::create($data);
    }
  }
}
