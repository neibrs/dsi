<?php

namespace Drupal\views_plus\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;

class ViewsPlusController extends ControllerBase {

  public function updateEditableField(Request $request) {
    $entity_type_id = $request->request->get('entity_type');
    $field = $request->request->get('entity_field');
    $keys = $request->request->get('keys');
    $value = $request->request->get('value');

    $entities = \Drupal::entityTypeManager()->getStorage($entity_type_id)->loadByProperties($keys);
    $entity = reset($entities);
    if (!$entity->access('update', \Drupal::currentUser())) {
      // TODO
    }
    // TODO: check field access

    $entity->set($field, $value);
    $entity->save();
  }

}
