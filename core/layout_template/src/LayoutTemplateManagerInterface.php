<?php

namespace Drupal\layout_template;

interface LayoutTemplateManagerInterface {

  /**
   * Returns the entity_form_display object used to build on entity form.
   *
   * @param string $entity_type
   *   The entity type
   * @param string $bundle
   *   The bundle.
   * @param string $form_mode
   *   The form mode.
   *
   * @return \Drupal\Core\Entity\Display\EntityFormDisplayInterface
   */
  public function getEntityFormDisplay($entity_type, $bundle, $form_mode);

  /**
   * @return \Drupal\Core\Entity\Display\EntityFormDisplayInterface
   */
  public function getEntityFormDisplayFromRoute();

  /**
   * @param string $type
   *   The layout template type.
   * @param string $related_config
   *   The config id.
   *
   * @return \Drupal\layout_template\Entity\LayoutTemplateInterface
   */
  public function currentLayoutTemplate($type, $related_config);

}
