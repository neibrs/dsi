<?php

namespace Drupal\organization\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Organization type entity.
 *
 * @ConfigEntityType(
 *   id = "organization_type",
 *   label = @Translation("Organization type"),
 *   label_collection = @Translation("Organization types"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\organization\OrganizationTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\organization\Form\OrganizationTypeForm",
 *       "edit" = "Drupal\organization\Form\OrganizationTypeForm",
 *       "delete" = "Drupal\eabax_core\Form\BundleDeleteForm"
 *     },
 *     "access" = "Drupal\organization\OrganizationTypeAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\organization\OrganizationTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "type",
 *   admin_permission = "administer organizations",
 *   bundle_of = "organization",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/organization/type/{organization_type}",
 *     "add-form" = "/organization/type/add",
 *     "edit-form" = "/organization/type/{organization_type}/edit",
 *     "delete-form" = "/organization/type/{organization_type}/delete",
 *     "collection" = "/organization/type"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *   }
 * )
 */
class OrganizationType extends ConfigEntityBundleBase implements OrganizationTypeInterface {

  /**
   * The Organization type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Organization type label.
   *
   * @var string
   */
  protected $label;

}
