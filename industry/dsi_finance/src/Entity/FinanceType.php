<?php

namespace Drupal\dsi_finance\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Finance type entity.
 *
 * @ConfigEntityType(
 *   id = "dsi_finance_type",
 *   label = @Translation("Finance type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dsi_finance\FinanceTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\dsi_finance\Form\FinanceTypeForm",
 *       "edit" = "Drupal\dsi_finance\Form\FinanceTypeForm",
 *       "delete" = "Drupal\dsi_finance\Form\FinanceTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dsi_finance\FinanceTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "dsi_finance_type",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/dsi_finance_type/{dsi_finance_type}",
 *     "add-form" = "/dsi_finance_type/add",
 *     "edit-form" = "/dsi_finance_type/{dsi_finance_type}/edit",
 *     "delete-form" = "/dsi_finance_type/{dsi_finance_type}/delete",
 *     "collection" = "/dsi_finance_type"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "target_entity_type_id",
 *   }
 * )
 */
class FinanceType extends ConfigEntityBase implements FinanceTypeInterface {

  /**
   * The Finance type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Finance type label.
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
}
