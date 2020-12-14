<?php

namespace Drupal\dsi_finance\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Finance detailed type entity.
 *
 * @ConfigEntityType(
 *   id = "dsi_finance_detailed_type",
 *   label = @Translation("Finance detailed type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dsi_finance\FinanceDetailedTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\dsi_finance\Form\FinanceDetailedTypeForm",
 *       "edit" = "Drupal\dsi_finance\Form\FinanceDetailedTypeForm",
 *       "delete" = "Drupal\dsi_finance\Form\FinanceDetailedTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dsi_finance\FinanceDetailedTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "dsi_finance_detailed_type",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/dsi_finance_detailed_type/{dsi_finance_detailed_type}",
 *     "add-form" = "/dsi_finance_detailed_type/add",
 *     "edit-form" = "/dsi_finance_detailed_type/{dsi_finance_detailed_type}/edit",
 *     "delete-form" = "/dsi_finance_detailed_type/{dsi_finance_detailed_type}/delete",
 *     "collection" = "/dsi_finance_detailed_type"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "target_entity_type_id",
 *   }
 * )
 */
class FinanceDetailedType extends ConfigEntityBase implements FinanceDetailedTypeInterface {

  /**
   * The Finance detailed type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Finance detailed type label.
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
