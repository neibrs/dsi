<?php

namespace Drupal\dsi_record\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'RecordByEntityBlock' block.
 *
 * @Block(
 *  id = "record_by_entity_block",
 *  admin_label = @Translation("Record by entity block"),
 * )
 */
class RecordByEntityBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /** @var \Drupal\Core\Entity\EntityStorageInterface */
  protected $recordStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
    $this->recordStorage = $entity_type_manager->getStorage('dsi_record');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration, $plugin_id, $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'record_by_entity_block';
    if (empty($this->configuration['entity_id']) || empty($this->configuration['entity_type'])) {
      return $build;
    }

    $query = $this->recordStorage->getQuery();
    $ids = $query
      ->condition('entity_type', $this->configuration['entity_type'])
      ->condition('entity_id', $this->configuration['entity_id'])
      ->execute();

    $entities = $this->recordStorage->loadMultiple($ids);
    $data = [];
    foreach ($entities as $key => $entity) {
      $data[$key] = [
        'name' => $entity->label(),
        // 'detail' => $entity->get('detail')->value,
      ];
    }
    $build['#content']['data'] = $data;

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }
}
