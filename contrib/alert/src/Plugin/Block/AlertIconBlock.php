<?php

namespace Drupal\alert\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'AlertIconBlock' block.
 *
 * @Block(
 *  id = "alert_icon_block",
 *  admin_label = @Translation("Alert icon block"),
 * )
 */
class AlertIconBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
  public function build() {
    $build = [];

    $storage = $this->entityTypeManager->getStorage('alert');
    $query = $storage->getQuery();
    $query->range(NULL, 10);
    $ids = $query->execute();
    $entities = $storage->loadMultiple($ids);

    $items = [];
    foreach ($entities as $entity) {
      $bundle = $entity->bundle();
      $definitions = \Drupal::entityTypeManager()->getDefinitions();
      $item = [
        'title' => $entity->label(),
        'created' => date('Y-m-d H:i:s', $entity->getCreatedTime()),
      ];
      if (in_array($bundle, array_keys($definitions))) {
        $item['type'] = $this->entityTypeManager->getStorage($bundle)->getEntityType()->getLabel();
      }
      $items[] = $item;
    }
    $menu = [
      '#theme' => 'item_list_a',
      '#items' => $items,
    ];

    $count = '<span class="label">' . count($ids) . '</span>';
    $build['icon_toggle'] = [
      '#theme' => 'dropdown_toggle',
      '#wrapper_attributes' => [
        'class' => ["notifications-menu"],
      ],
      '#attributes' => [],
      '#icon_pre' => ['#markup' => 'fa fa-bell'],
      '#icon' => ['#markup' => $count],
      '#items' => $menu,
      '#title' => $this->t('You have %num notifications', ['%num' => count($ids)]),
      '#empty' => $this->t('No notifications.'),
      '#more_link' => Url::fromRoute('entity.alert.collection'),
    ];
    return $build;
  }

  /**
   * @return array|string[]
   */
  public function getCacheTags() {
    $cache_tags = parent::getCacheTags();

    return Cache::mergeTags($cache_tags, ['alert_list']);
  }

}
