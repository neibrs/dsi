<?php

namespace Drupal\import\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\migrate\Plugin\MigrationPluginManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ImportController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\migrate\Plugin\MigrationPluginManagerInterface
   */
  protected $migrationPluginManager;

  /**
   * ImportController constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, MigrationPluginManagerInterface $migration_plugin_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->migrationPluginManager = $migration_plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.migration')
    );
  }

  public function getTemplate($entity_type_id, $migration_id) {
    $definition = $this->migrationPluginManager->getDefinition($migration_id);
    $path = $definition['source']['path'];
    $source = DRUPAL_ROOT . '/' . $path;

    $uri = file_unmanaged_copy($source, 'private://export', FILE_EXISTS_RENAME);
    $url = file_create_url($uri);

    return new RedirectResponse($url);
  }

  /**
   * @param $entity_type_id
   * @description for import page title callback.
   */
  public function getImportTitle($entity_type_id) {
    $entity_type = $this->entityTypeManager->getStorage($entity_type_id)->getEntityType();

    return $this->t('@entity_type import', ['@entity_type' => $entity_type->getLabel()]);
  }

}
