<?php

namespace Drupal\organization\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'ChildrenOrganizationBlock' block.
 *
 * @Block(
 *  id = "children_organization_block",
 *  admin_label = @Translation("Children organization block"),
 * )
 */
class ChildrenOrganizationBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  public $currentRouteMatch;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $current_route_match) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->currentRouteMatch = $current_route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $organization = $this->currentRouteMatch->getParameter('organization');
    if (!$organization) {
      return [];
    }

    $build = [];

    $links['add'] = [
      'title' => $this->t('Add'),
      'url' => Url::fromRoute('entity.organization.children.add', [
        'organization' => $organization->id(),
      ], [
        'query' => \Drupal::destination()->getAsArray(),
      ]),
    ];
    $build['links'] = [
      '#theme' => 'links__btn_group',
      '#links' => $links,
    ];
    $build['children'] = views_embed_view('organization_children', 'default', $organization->id());

    return $build;
  }

}
