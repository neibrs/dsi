<?php

namespace Drupal\import\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Excel migrate entity.
 *
 * @ConfigEntityType(
 *   id = "excel_migrate",
 *   label = @Translation("Excel migrate"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\import\ExcelMigrateListBuilder",
 *     "form" = {
 *       "add" = "Drupal\import\Form\ExcelMigrateForm",
 *       "edit" = "Drupal\import\Form\ExcelMigrateForm",
 *       "delete" = "Drupal\import\Form\ExcelMigrateDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\import\ExcelMigrateHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "excel_migrate",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "add-form" = "/excel_migrate/add",
 *     "edit-form" = "/excel_migrate/{excel_migrate}/edit",
 *     "delete-form" = "/excel_migrate/{excel_migrate}/delete",
 *     "collection" = "/excel_migrate",
 *     "import-form" = "/excel_migrate/{excel_migrate}/import",
 *   }
 * )
 */
class ExcelMigrate extends ConfigEntityBase implements ExcelMigrateInterface {

  /**
   * The Excel migrate ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Excel migrate label.
   *
   * @var string
   */
  protected $label;

  protected $source;

  protected $constants;

  protected $sheets = [];

  public function getSource() {
    return $this->source;
  }

  public function setSource($source) {
    $this->source = $source;
  }

  public function getConstants() {
    return $this->constants;
  }

  public function setConstant($key, $value) {
    $this->constants[$key] = $value;
  }

  public function getSheets() {
    return $this->sheets;
  }

  public function setSheets($sheets) {
    $this->sheets = $sheets;
  }

}
