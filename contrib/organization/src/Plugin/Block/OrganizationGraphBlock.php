<?php

namespace Drupal\organization\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\organization\Entity\OrganizationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'OrganizationGraphBlock' block.
 *
 * @Block(
 *  id = "organization_graph_block",
 *  admin_label = @Translation("Organization graph block"),
 * )
 */
class OrganizationGraphBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\organization\OrganizationStorageInterface
   */
  protected $organizationStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->organizationStorage = $entity_type_manager->getStorage('organization');
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

    /** @var \Drupal\person\Entity\PersonInterface $person */
    if (!$person = \Drupal::service('person.manager')->currentPerson()) {
      return [];
    }
    if (!$organization = $person->getOrganizationByClassification('business_group')) {
      return [];
    }

    $item = $this->getTreeItem($organization);
    $items = [$item];

    $build['organization_graph_block'] = [
      '#theme' => 'organization_graph',
      '#items' => $items,
      '#attributes' => ['id' => 'org', 'class' => ['tab-scroll']],
      '#attached' => [
        'library' => [
          'organization/graph',
        ],
      ],
    ];

    return $build;
  }

  protected function getTreeItem(OrganizationInterface $organization) {
    $item = [
      'title' => $organization->label(),
      'url' => $organization->toUrl(),
    ];

    $children = $this->organizationStorage->loadByProperties([
      'parent' => $organization->id(),
    ]);
    foreach ($children as $child) {
      $below[] = $this->getTreeItem($child);
    }
    if (isset($below)) {
      $item['below'] = $below;
    }

    return $item;
  }

}
