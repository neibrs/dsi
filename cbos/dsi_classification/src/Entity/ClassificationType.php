<?php

namespace Drupal\dsi_classification\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Classification type entity.
 *
 * @ConfigEntityType(
 *   id = "dsi_classification_type",
 *   label = @Translation("Classification type"),
 *   label_collection = @Translation("Classification type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dsi_classification\ClassificationTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\dsi_classification\Form\ClassificationTypeForm",
 *       "edit" = "Drupal\dsi_classification\Form\ClassificationTypeForm",
 *       "delete" = "Drupal\dsi_classification\Form\ClassificationTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dsi_classification\ClassificationTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "type",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/dsi_classification_type/{dsi_classification_type}",
 *     "add-form" = "/admin/dsi_classification_type/add",
 *     "edit-form" = "/admin/dsi_classification_type/{dsi_classification_type}/edit",
 *     "delete-form" = "/admin/dsi_classification_type/{dsi_classification_type}/delete",
 *     "collection" = "/admin/dsi_classification_type"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *   }
 * )
 */
class ClassificationType extends ConfigEntityBase implements ClassificationTypeInterface {

  /**
   * The Classification type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Classification type label.
   *
   * @var string
   */
  protected $label;

}
