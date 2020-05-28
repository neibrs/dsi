<?php

namespace Drupal\layout_template\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\layout_template\Entity\LayoutTemplateTypeInterface;

/**
 * Returns responses for Layout templates.
 */
class LayoutTemplateController extends ControllerBase {

  public function add(LayoutTemplateTypeInterface $layout_template_type, $related_config) {
    $entity = $this->entityTypeManager()->getStorage('layout_template')->create([
      'type' => $layout_template_type->id(),
      'related_config' => $related_config,
    ]);

    $form = $this->entityFormBuilder()->getForm($entity);

    return $form;
  }

}
