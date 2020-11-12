<?php

namespace Drupal\organization\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Organization classification entity.
 *
 * @ConfigEntityType(
 *   id = "organization_classification",
 *   label = @Translation("Organization classification"),
 *   config_prefix = "classification",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *   }
 * )
 */
class OrganizationClassification extends ConfigEntityBase implements OrganizationClassificationInterface {

  /**
   * The Organization classification ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Organization classification label.
   *
   * @var string
   */
  protected $label;

  /**
   * A brief description of this classification.
   *
   * @var string
   */
  protected $description;

}
