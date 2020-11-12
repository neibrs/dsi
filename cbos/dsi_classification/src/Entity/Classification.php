<?php

namespace Drupal\dsi_classification\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Classification entity.
 *
 * @ConfigEntityType(
 *   id = "dsi_classification",
 *   label = @Translation("Classification"),
 *   label_collection = @Translation("Classification"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dsi_classification\ClassificationListBuilder",
 *     "form" = {
 *       "add" = "Drupal\dsi_classification\Form\ClassificationForm",
 *       "edit" = "Drupal\dsi_classification\Form\ClassificationForm",
 *       "delete" = "Drupal\dsi_classification\Form\ClassificationDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dsi_classification\ClassificationHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "classification",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/dsi_classification/{dsi_classification}",
 *     "add-form" = "/admin/dsi_classification/add",
 *     "edit-form" = "/admin/dsi_classification/{dsi_classification}/edit",
 *     "delete-form" = "/admin/dsi_classification/{dsi_classification}/delete",
 *     "collection" = "/admin/dsi_classification"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "target_entity_type_id",
 *     "target_entity_bundle_id",
 *     "collections",
 *   }
 * )
 */
class Classification extends ConfigEntityBase implements ClassificationInterface {

  /**
   * The Classification ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Classification label.
   *
   * @var string
   */
  protected $label;

  protected $target_entity_type_id;

  protected $target_entity_bundle_id;

  protected $collections;

  /**
   * {@inheritDoc}
   */
  public function getTargetEntityTypeId() {
    return $this->target_entity_type_id;
  }

  /**
   * {@inheritDoc}
   */
  public function setTargetEntityTypeId($target_entity_type_id) {
    $this->target_entity_type_id = $target_entity_type_id;

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function getCollections() {
    return $this->collections;
  }

  /**
   * {@inheritDoc}
   */
  public function setCollections($collections = []) {
    $this->collections = $collections;

    return $this;
  }

  public function getTargetEntityBundleId() {
    return $this->target_entity_bundle_id;
  }

  public function setTargetEntityBundleId($target_entity_bundle_id = NULL) {
    return $this->target_entity_bundle_id = $target_entity_bundle_id;
  }

}
