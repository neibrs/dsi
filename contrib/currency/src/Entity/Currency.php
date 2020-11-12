<?php

namespace Drupal\currency\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Currency entity.
 *
 * @ConfigEntityType(
 *   id = "currency",
 *   label = @Translation("Currency"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\currency\CurrencyListBuilder",
 *     "form" = {
 *       "add" = "Drupal\currency\Form\CurrencyForm",
 *       "edit" = "Drupal\currency\Form\CurrencyForm",
 *       "delete" = "Drupal\currency\Form\CurrencyDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\currency\CurrencyHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "currency",
 *   admin_permission = "administer currencies",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/currency/{currency}",
 *     "add-form" = "/currency/add",
 *     "edit-form" = "/currency/{currency}/edit",
 *     "delete-form" = "/currency/{currency}/delete",
 *     "collection" = "/currency"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "symbol",
 *     "precision",
 *   }
 * )
 */
class Currency extends ConfigEntityBase implements CurrencyInterface {

  /**
   * The Currency ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Currency label.
   *
   * @var string
   */
  protected $label;

  protected $symbol;
  protected $precision;

}
