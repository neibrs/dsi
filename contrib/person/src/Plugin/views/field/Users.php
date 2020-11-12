<?php

namespace Drupal\person\Plugin\views\field;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\field\PrerenderList;
use Drupal\views\ViewExecutable;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Field handler to provide a list of users.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("person_users")
 */
class Users extends PrerenderList implements CacheableDependencyInterface {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

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
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);

    $this->additional_fields['id'] = ['table' => 'person_field_data', 'field' => 'id'];
  }

  public function query() {
    $this->addAdditionalFields();
    $this->field_alias = $this->aliases['id'];
  }

  public function preRender(&$values) {
    $this->items = [];

    $user_storage = $this->entityTypeManager->getStorage('user');

    // TODO load all users once.
    foreach ($values as $result) {
      $id = $this->getValue($result);
      $users = $user_storage->loadByProperties(['person' => $id]);
      /** @var \Drupal\user\UserInterface $user */
      foreach ($users as $user) {
        $this->items[$id][$user->id()]['user'] = $user->label();
        $this->items[$id][$user->id()]['user_id'] = $user->id();
        $this->items[$id][$user->id()]['is_blocked'] = $user->isBlocked();
      }
    }
  }

  public function render_item($count, $item) {
    return $item['user'];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return $this->entityTypeManager->getDefinition('user')->getListCacheTags();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return Cache::PERMANENT;
  }

}
