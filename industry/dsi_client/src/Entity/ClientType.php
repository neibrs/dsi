<?php

namespace Drupal\dsi_client\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Client type entity.
 *
 * @ConfigEntityType(
 *   id = "dsi_client_type",
 *   label = @Translation("Client type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dsi_client\ClientTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\dsi_client\Form\ClientTypeForm",
 *       "edit" = "Drupal\dsi_client\Form\ClientTypeForm",
 *       "delete" = "Drupal\dsi_client\Form\ClientTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dsi_client\ClientTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "dsi_client_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "dsi_client",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/dsi_client_type/{dsi_client_type}",
 *     "add-form" = "/dsi_client_type/add",
 *     "edit-form" = "/dsi_client_type/{dsi_client_type}/edit",
 *     "delete-form" = "/dsi_client_type/{dsi_client_type}/delete",
 *     "collection" = "/dsi_client_type"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "target_entity_type_id",
 *   }
 * )
 */
class ClientType extends ConfigEntityBundleBase implements ClientTypeInterface {

  /**
   * The Client type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Client type label.
   *
   * @var string
   */
  protected $label;

  /**
   * The target entity type.
   *
   * @var string
   */
  protected $target_entity_type_id;

  /**
   * {@inheritdoc}
   */
  public function getTargetEntityTypeId() {
    return $this->target_entity_type_id;
  }

  // TODO, getTargetEntity.
}
