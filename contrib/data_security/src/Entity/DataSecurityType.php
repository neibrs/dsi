<?php

namespace Drupal\data_security\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\Core\Entity\EntityDescriptionInterface;

/**
 * Defines the Data security type entity.
 *
 * @ConfigEntityType(
 *   id = "data_security_type",
 *   label = @Translation("Data scope"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\data_security\DataSecurityTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\data_security\Form\DataSecurityTypeForm",
 *       "edit" = "Drupal\data_security\Form\DataSecurityTypeForm",
 *       "delete" = "Drupal\data_security\Form\DataSecurityTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\data_security\DataSecurityTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "data_security",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/data_security_type/{data_security_type}",
 *     "add-form" = "/data_security_type/add",
 *     "edit-form" = "/data_security_type/{data_security_type}/edit",
 *     "delete-form" = "/data_security_type/{data_security_type}/delete",
 *     "collection" = "/data_security_type"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *   }
 * )
 */
class DataSecurityType extends ConfigEntityBundleBase implements DataSecurityTypeInterface {

  /**
   * The Data security type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Data security type label.
   *
   * @var string
   */
  protected $label;

  /**
   * A brief description of this type.
   *
   * @var string
   */
  protected $description;

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description) {
    $this->description = $description;
    return $this;
  }
}
