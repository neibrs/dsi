<?php

namespace Drupal\dsi_device\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Device type entity.
 *
 * @ConfigEntityType(
 *   id = "dsi_device_type",
 *   label = @Translation("Device type"),
 *   label_collection = @Translation("Device type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dsi_device\DeviceTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\dsi_device\Form\DeviceTypeForm",
 *       "edit" = "Drupal\dsi_device\Form\DeviceTypeForm",
 *       "delete" = "Drupal\dsi_device\Form\DeviceTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dsi_device\DeviceTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "dsi_device",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/device/config/dsi_device_type/{dsi_device_type}",
 *     "add-form" = "/admin/device/config/dsi_device_type/add",
 *     "edit-form" = "/admin/device/config/dsi_device_type/{dsi_device_type}/edit",
 *     "delete-form" = "/admin/device/config/dsi_device_type/{dsi_device_type}/delete",
 *     "collection" = "/admin/device/config/dsi_device_type"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *   }
 * )
 */
class DeviceType extends ConfigEntityBundleBase implements DeviceTypeInterface {

  /**
   * The Device type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Device type label.
   *
   * @var string
   */
  protected $label;

}
