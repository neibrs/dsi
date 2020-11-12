<?php

namespace Drupal\lookup\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Lookup type entity.
 *
 * @ConfigEntityType(
 *   id = "lookup_type",
 *   label = @Translation("Lookup type", context="Codes"),
 *   label_collection = @Translation("Lookup types", context="Codes"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\lookup\LookupTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\lookup\Form\LookupTypeForm",
 *       "edit" = "Drupal\lookup\Form\LookupTypeForm",
 *       "delete" = "Drupal\lookup\Form\LookupTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\lookup\LookupTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "type",
 *   admin_permission = "administer lookups",
 *   bundle_of = "lookup",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/lookup/type/{lookup_type}",
 *     "add-form" = "/lookup/type/add",
 *     "edit-form" = "/lookup/type/{lookup_type}/edit",
 *     "collection" = "/lookup/type",
 *     "lookup" = "/lookup/type/{lookup_type}/lookup",
 *     "disable" = "/lookup/type/{lookup_type}/disable",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *   },
 * )
 */
class LookupType extends ConfigEntityBundleBase implements LookupTypeInterface {

  /**
   * The Lookup type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Lookup type label.
   *
   * @var string
   */
  protected $label;

  /**
   * A brief description of this lookup type.
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
