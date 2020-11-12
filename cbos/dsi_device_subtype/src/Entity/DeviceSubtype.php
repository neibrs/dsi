<?php

namespace Drupal\dsi_device_subtype\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Device subtype entity.
 *
 * @ConfigEntityType(
 *   id = "dsi_device_subtype",
 *   label = @Translation("Device subtype"),
 *   label_collection = @Translation("Device subtype"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dsi_device_subtype\DeviceSubtypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\dsi_device_subtype\Form\DeviceSubtypeForm",
 *       "edit" = "Drupal\dsi_device_subtype\Form\DeviceSubtypeForm",
 *       "delete" = "Drupal\dsi_device_subtype\Form\DeviceSubtypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dsi_device_subtype\DeviceSubtypeHtmlRouteProvider",
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
 *     "canonical" = "/admin/dsi_device_subtype/{dsi_device_subtype}",
 *     "add-form" = "/admin/dsi_device_subtype/add",
 *     "edit-form" = "/admin/dsi_device_subtype/{dsi_device_subtype}/edit",
 *     "delete-form" = "/admin/dsi_device_subtype/{dsi_device_subtype}/delete",
 *     "collection" = "/admin/dsi_device_subtype"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *   }
 * )
 */
class DeviceSubtype extends ConfigEntityBase implements DeviceSubtypeInterface {

  /**
   * The Device subtype ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Device subtype label.
   *
   * @var string
   */
  protected $label;

}
