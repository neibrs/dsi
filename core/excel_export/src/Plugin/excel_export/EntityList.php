<?php

namespace Drupal\excel_export\Plugin\excel_export;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\excel_export\Entity\ExcelExportInterface;
use Drupal\excel_export\Plugin\ExcelExportPluginBase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @ExcelExport(
 *   id = "entity_list",
 *   title = @Translation("Entity List")
 * )
 */
class EntityList extends ExcelExportPluginBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function process(ExcelExportInterface $excel_export, Spreadsheet $sheet) {
    if ($row = $this->configuration['row']) {
      if ($row == 'down') {
        $row = $sheet->getCell($sheet->getActiveCell())->getRow();
        $row++;
      }
    }
    else {
      $row = $sheet->getCell($sheet->getActiveCell())->getRow();
    }
    $cell = reset(array_keys($this->configuration['columns'])) . $row;

    $data = $this->buildValue($excel_export);

    $sheet->fromArray($data, NULL, strtoupper($cell));
  }

  public function buildValue(ExcelExportInterface $excel_export) {
    $parameters = $excel_export->getParameters();

    $storage = $this->entityTypeManager->getStorage($this->configuration['entity_type']);

    // Load entities
    $query = $storage->getQuery();
    foreach ($this->configuration['condition'] as $key => $value) {
      $a = explode('/', $value);
      if ($a[0] == 'parameters') {
        $value = $parameters[$a[1]];
      }
      $query->condition($key, $value);
    }

    if ($order_by = $this->configuration['order_by']) {
      $direction = 'ASC';
      if ($order_direction = $this->configuration['order_direction']) {
        $direction = $order_direction;
      }
      $query->sort($order_by, $direction);
    }
    $ids = $query->execute();
    $entities = $storage->loadMultiple($ids);

    // Build data array
    $data = [];
    $columns = array_keys($this->configuration['columns']);
    $first_column = reset($columns);
    $last_column = array_pop($columns);
    $columns = $this->configuration['columns'];
    foreach ($entities as $entity) {
      $column = $first_column;
      $values = [];
      while ($column <= $last_column) {
        if (isset($columns[$column])) {
          $values[] = $entity->get($columns[$column])->value;
        }
        else {
          $values[] = NULL;
        }

        $column++;
      }
      $data[] = $values;
    }

    return $data;
  }

}
