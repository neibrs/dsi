<?php

namespace Drupal\options_plus\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the String options entity.
 *
 * @ConfigEntityType(
 *   id = "string_options",
 *   label = @Translation("String options"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\options_plus\StringOptionsListBuilder",
 *     "form" = {
 *       "add" = "Drupal\options_plus\Form\StringOptionsForm",
 *       "edit" = "Drupal\options_plus\Form\StringOptionsForm",
 *       "delete" = "Drupal\options_plus\Form\StringOptionsDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\options_plus\StringOptionsHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "string_options",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/string_options/{string_options}",
 *     "add-form" = "/string_options/add",
 *     "edit-form" = "/string_options/{string_options}/edit",
 *     "delete-form" = "/string_options/{string_options}/delete",
 *     "collection" = "/string_options"
 *   }
 * )
 */
class StringOptions extends ConfigEntityBase implements StringOptionsInterface {

  /**
   * The String options ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The String options label.
   *
   * @var string
   */
  protected $label;

  protected $allowed_values = [];

  /**
   * {@inheritdoc}
   */
  public function getAllowedValues() {
    return $this->allowed_values;
  }

}
