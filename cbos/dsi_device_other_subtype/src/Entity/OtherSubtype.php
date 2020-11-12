<?php

namespace Drupal\dsi_device_other_subtype\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Other subtype entity.
 *
 * @ConfigEntityType(
 *   id = "dsi_device_other_subtype",
 *   label = @Translation("Other subtype"),
 *   label_collection = @Translation("Other subtype"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dsi_device_other_subtype\OtherSubtypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\dsi_device_other_subtype\Form\OtherSubtypeForm",
 *       "edit" = "Drupal\dsi_device_other_subtype\Form\OtherSubtypeForm",
 *       "delete" = "Drupal\dsi_device_other_subtype\Form\OtherSubtypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dsi_device_other_subtype\OtherSubtypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "subtype",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/dsi_device_other_subtype/{dsi_device_other_subtype}",
 *     "add-form" = "/admin/dsi_device_other_subtype/add",
 *     "edit-form" = "/admin/dsi_device_other_subtype/{dsi_device_other_subtype}/edit",
 *     "delete-form" = "/admin/dsi_device_other_subtype/{dsi_device_other_subtype}/delete",
 *     "collection" = "/admin/dsi_device_other_subtype"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "locations",
 *   }
 * )
 */
class OtherSubtype extends ConfigEntityBase implements OtherSubtypeInterface {

  /**
   * The Other subtype ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Other subtype label.
   *
   * @var string
   */
  protected $label;

  protected $locations;

  /**
   * {@inheritDoc}
   */
  public function getLocations() {
    return $this->locations;
  }

  /**
   * {@inheritDoc}
   */
  public function setLocations($locations = []) {
    $this->locations = $locations;
    return $this;
  }

}
