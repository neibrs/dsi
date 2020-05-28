<?php

namespace Drupal\excel_export\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Excel export entity.
 *
 * @ConfigEntityType(
 *   id = "excel_export",
 *   label = @Translation("Excel export"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\excel_export\ExcelExportListBuilder",
 *     "form" = {
 *       "add" = "Drupal\excel_export\Form\ExcelExportForm",
 *       "edit" = "Drupal\excel_export\Form\ExcelExportForm",
 *       "delete" = "Drupal\excel_export\Form\ExcelExportDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\excel_export\ExcelExportHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "template",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/excel_export/{excel_export}",
 *     "add-form" = "/excel_export/add",
 *     "edit-form" = "/excel_export/{excel_export}/edit",
 *     "delete-form" = "/excel_export/{excel_export}/delete",
 *     "collection" = "/excel_export",
 *     "export" = "/excel_export/{excel_export}/export",
 *   }
 * )
 */
class ExcelExport extends ConfigEntityBase implements ExcelExportInterface {

  /**
   * The Excel export ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Excel export label.
   *
   * @var string
   */
  protected $label;

  /**
   * @var array
   */
  protected $parameters;

  /**
   * @var array
   */
  protected $sheets;

  /**
   * {@inheritdoc}
   */
  public function getParameters() {
    return $this->parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function setParameters($parameters) {
    $this->parameters = $parameters;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSheets() {
    return $this->sheets;
  }

  /**
   * {@inheritdoc}
   */
  public function setSheets($sheets) {
    $this->sheets = $sheets;
    return $this;
  }

}
