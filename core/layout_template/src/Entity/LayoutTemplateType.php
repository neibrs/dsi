<?php

namespace Drupal\layout_template\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Layout template type entity.
 *
 * @ConfigEntityType(
 *   id = "layout_template_type",
 *   label = @Translation("Layout template type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\layout_template\LayoutTemplateTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\layout_template\Form\LayoutTemplateTypeForm",
 *       "edit" = "Drupal\layout_template\Form\LayoutTemplateTypeForm",
 *       "delete" = "Drupal\layout_template\Form\LayoutTemplateTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\layout_template\LayoutTemplateTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "layout_template",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/layout_template_type/{layout_template_type}",
 *     "add-form" = "/layout_template_type/add",
 *     "edit-form" = "/layout_template_type/{layout_template_type}/edit",
 *     "delete-form" = "/layout_template_type/{layout_template_type}/delete",
 *     "collection" = "/layout_template_type"
 *   }
 * )
 */
class LayoutTemplateType extends ConfigEntityBundleBase implements LayoutTemplateTypeInterface {

  /**
   * The Layout template type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Layout template type label.
   *
   * @var string
   */
  protected $label;

}
