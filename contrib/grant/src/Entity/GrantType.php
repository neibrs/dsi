<?php

namespace Drupal\grant\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Grant type entity.
 *
 * @ConfigEntityType(
 *   id = "grant_type",
 *   label = @Translation("Grantee type"),
 *   config_prefix = "grantee_type",
 *   bundle_of = "grant",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "target_entity_type_id",
 *   }
 * )
 */
class GrantType extends ConfigEntityBundleBase implements GrantTypeInterface {

  /**
   * The Grant type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Grant type label.
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
   * The target entity type.
   *
   * @var string
   */
  protected $target_entity_type_id;

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

  /**
   * {@inheritdoc}
   */
  public function getTargetEntityTypeId() {
    return $this->target_entity_type_id;
  }

}
