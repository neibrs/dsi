<?php

namespace Drupal\dsi_hardware\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Hardware type entity.
 *
 * @ConfigEntityType(
 *   id = "dsi_hardware_type",
 *   label = @Translation("Hardware type"),
 *   label_collection = @Translation("Hardware type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dsi_hardware\HardwareTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\dsi_hardware\Form\HardwareTypeForm",
 *       "edit" = "Drupal\dsi_hardware\Form\HardwareTypeForm",
 *       "delete" = "Drupal\dsi_hardware\Form\HardwareTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dsi_hardware\HardwareTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "dsi_hardware",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/dsi_hardware_type/{dsi_hardware_type}",
 *     "add-form" = "/dsi_hardware_type/add",
 *     "edit-form" = "/dsi_hardware_type/{dsi_hardware_type}/edit",
 *     "delete-form" = "/dsi_hardware_type/{dsi_hardware_type}/delete",
 *     "collection" = "/dsi_hardware_type"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *   }
 * )
 */
class HardwareType extends ConfigEntityBundleBase implements HardwareTypeInterface {

  /**
   * The Hardware type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Hardware type label.
   *
   * @var string
   */
  protected $label;

}
