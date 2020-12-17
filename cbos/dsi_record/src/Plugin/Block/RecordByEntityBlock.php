<?php

namespace Drupal\dsi_record\Plugin\Block;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
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

  /**
 * @var \Drupal\Core\Entity\EntityStorageInterface */
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
        'detail' => ['#markup' => strip_tags($entity->get('detail')->value)],
        'created' => date('Y-m-d H:i', $entity->getCreatedTime()),
      ];
    }
    $build['#content']['add_link'] = [
      '#type' => 'link',
      '#title' => $this->t('Add'),
      '#url' => Url::fromRoute('entity.dsi_record.add_todo', [
        'entity_type' => $this->configuration['entity_type'],
        'entity_id' => $this->configuration['entity_id'],
      ],
        [
          'query' => ['destination' => '/dsi_client'],
        ]
      ),
          '#options' => [
          'attributes' => [
            'class' => ['use-ajax'],
            'data-dialog-type' => 'modal',
            'data-dialog-options' => Json::encode([
            'width' => 800,
          ]),
        ],
      ],
    ];
    $build['#content']['data'] = $data;
    return $build;
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheTags() {
    $cache_tags = parent::getCacheTags();

    return Cache::mergeTags($cache_tags, ['dsi_record_list']);
  }

}
