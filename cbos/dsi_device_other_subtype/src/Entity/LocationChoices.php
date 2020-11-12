<?php

namespace Drupal\dsi_device_other_subtype\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Location choices entity.
 *
 * @ConfigEntityType(
 *   id = "dsi_device_oslc",
 *   label = @Translation("Location choices"),
 *   label_collection = @Translation("Location choices"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dsi_device_other_subtype\LocationChoicesListBuilder",
 *     "form" = {
 *       "add" = "Drupal\dsi_device_other_subtype\Form\LocationChoicesForm",
 *       "edit" = "Drupal\dsi_device_other_subtype\Form\LocationChoicesForm",
 *       "delete" = "Drupal\dsi_device_other_subtype\Form\LocationChoicesDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dsi_device_other_subtype\LocationChoicesHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "oslc",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/dsi_device_oslc/{dsi_device_oslc}",
 *     "add-form" = "/admin/dsi_device_oslc/add",
 *     "edit-form" = "/admin/dsi_device_oslc/{dsi_device_oslc}/edit",
 *     "collection" = "/admin/dsi_device_oslc"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *   }
 * )
 */
class LocationChoices extends ConfigEntityBase implements LocationChoicesInterface {

  /**
   * The Location choices ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Location choices label.
   *
   * @var string
   */
  protected $label;

}
