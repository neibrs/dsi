<?php

namespace Drupal\dsi_finance\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Finance expenditure type entity.
 *
 * @ConfigEntityType(
 *   id = "dsi_finance_expenditure_type",
 *   label = @Translation("Finance expenditure type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dsi_finance\FinanceExpenditureTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\dsi_finance\Form\FinanceExpenditureTypeForm",
 *       "edit" = "Drupal\dsi_finance\Form\FinanceExpenditureTypeForm",
 *       "delete" = "Drupal\dsi_finance\Form\FinanceExpenditureTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dsi_finance\FinanceExpenditureTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "dsi_finance_expenditure_type",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/dsi_finance_expenditure_type/{dsi_finance_expenditure_type}",
 *     "add-form" = "/dsi_finance_expenditure_type/add",
 *     "edit-form" = "/dsi_finance_expenditure_type/{dsi_finance_expenditure_type}/edit",
 *     "delete-form" = "/dsi_finance_expenditure_type/{dsi_finance_expenditure_type}/delete",
 *     "collection" = "/dsi_finance_expenditure_type"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "target_entity_type_id",
 *   }
 * )
 */
class FinanceExpenditureType extends ConfigEntityBase implements FinanceExpenditureTypeInterface {

  /**
   * The Finance expenditure type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Finance expenditure type label.
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
