<?php

namespace Drupal\excel_export\Plugin\excel_export;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\excel_export\Entity\ExcelExportInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @ExcelExport(
 *   id = "entity_cell",
 *   title = @Translation("Entity Cell")
 * )
 */
class EntityCell extends Cell implements ContainerFactoryPluginInterface {

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

  protected function buildValue(ExcelExportInterface $excel_export) {
    $properties = [];
    $parameters = $excel_export->getParameters();
    foreach ($this->configuration['condition'] as $field => $value) {
      $a = explode('/', $value);
      if ($a[0] == 'parameters') {
        $value = $parameters[$a[1]];
      }
      $properties[$field] = $value;
    }

    $entities = $this->entityTypeManager
      ->getStorage($this->configuration['entity_type'])
      ->loadByProperties($properties);
    if ($entity = reset($entities)) {
      if ($relationship = $this->configuration['relationship']) {
        $entity = $entity->get($relationship)->entity;
      }
      return $entity->get($this->configuration['field'])->value;
    }
  }

}
