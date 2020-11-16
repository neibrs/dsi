<?php

namespace Drupal\lookup;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InstallHelper implements ContainerInjectionInterface {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $lookupStorage;
  /**
   * {@inheritDoc}
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->lookupStorage = $entity_type_manager->getStorage('lookup');
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * @param $type
   * @param $data
   * @param $description
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function getOrCreateLookup($type, $data, $description) {
    foreach ($data as $key => $value) {
      $values = [
        'type' => $type,
        'name' => $value,
        'code' => $key,
        'description' => $description,
      ];
      $lookup = $this->lookupStorage->loadByProperties($values);
      if (empty($lookup)) {
        $this->lookupStorage->create($values)->save();
      }
    }
  }
}
