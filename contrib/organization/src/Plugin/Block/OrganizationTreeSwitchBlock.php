<?php

namespace Drupal\organization\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;
use Drupal\organization\Entity\OrganizationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'OrganizationTreeSwitchBlock' block.
 *
 * @Block(
 *  id = "organization_tree_switch",
 *  admin_label = @Translation("Organization tree switch"),
 * )
 */
class OrganizationTreeSwitchBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\organization\OrganizationStorageInterface
   */
  protected $organizationStorage;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
    $this->organizationStorage = $entity_type_manager->getStorage('organization');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static (
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
    $route_match = \Drupal::routeMatch();
    /** @var \Drupal\organization\Entity\OrganizationInterface $organization */
    $organization = $route_match->getParameter('organization');
    if (!$organization) {
      return [];
    }

    if (!is_object($organization)) {
      $organization = $this->organizationStorage->load($organization);
    }

    $current = $organization;
    $active_trails = [$current->id()];
    while ((!$current->hasClassification('business_group')) && ($parent = $current->getParent())) {
      $active_trails[] = $parent->id();
      $current = $parent;
    }

    $route_name = $route_match->getRouteName();
    $parameters = $route_match->getRawParameters()->getIterator()->getArrayCopy();

    $item = $this->buildItem($current, $active_trails, $route_name, $parameters);

    $build['tree'] = [
      '#theme' => 'tree',
      '#items' => [$item],
      '#prefix' => '<div class="jstree">',
      '#suffix' => '</div>',
    ];
    $build['tree']['#attached']['library'][] = 'eabax_core/jstree';

    return $build;
  }

  protected function buildItem(OrganizationInterface $organization, $active_trails, $route_name, $route_parameters) {
    $item = [
      'title' => $organization->label(),
      'url' => Url::fromRoute($route_name, ['organization' => $organization->id()] + $route_parameters),
    ];

    $children = $this->organizationStorage->loadByProperties([
      'parent' => $organization->id(),
      'status' => 1,
    ]);
    $below = [];
    foreach ($children as $child) {
      $below[] = $this->buildItem($child, $active_trails, $route_name, $route_parameters);
    }
    $options = [];
    if (!empty($below)) {
      $item['below'] = $below;
      if (in_array($organization->id(), $active_trails)) {
        $item['is_expanded'] = TRUE;
        $options['opened'] = TRUE;
      }
    }

    if ($organization->id() == reset($active_trails)) {
      $options['selected'] = TRUE;
    }
    $attributes = [];
    if (!empty($options)) {
      $attributes['data-jstree'] = json_encode($options);
    }
    $item['attributes'] = new Attribute($attributes);

    return $item;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), $this->entityTypeManager->getDefinition('organization')->getListCacheTags());
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url']);
  }

}
