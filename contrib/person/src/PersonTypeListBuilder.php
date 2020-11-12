<?php

namespace Drupal\person;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a listing of Person type entities.
 */
class PersonTypeListBuilder extends ConfigEntityListBuilder {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')->getStorage($entity_type->id()),
      $container->get('entity_type.manager')
    );
  }

  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($entity_type, $storage);

    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Person type');
    $header['alias'] = $this->t('Person type alias');
    $header['system_type'] = $this->t('System type');
    $header['is_system'] = $this->t('Is system');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['alias'] = $entity->getAlias();

    $system_types = [
      'employee' => $this->t('Employee'),
      'applicant' => $this->t('Applicant'),
      'contingent_worker' => $this->t('Contingent worker'),
      'ex_employee' => $this->t('Ex-employee'),
      'ex_applicant' => $this->t('Ex-applicant'),
      'ex_contingent_worker' => $this->t('Ex-contingent worker'),
      'external' => $this->t('External')
    ];
    
    if (isset($system_types[$entity->getSystemType()]) && $system_type = $system_types[$entity->getSystemType()]) {
      $row['system_type'] = $system_type;
    }
    else {
      $row['system_type'] = '';
    }
  
    $row['is_system'] = $entity->getIsSystem() ? $this->t('Yes') : $this->t('No');

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function load() {
    $entities = [
      'enabled' => [],
      'disabled' => [],
    ];
    foreach (parent::load() as $entity) {
      if ($entity->status()) {
        $entities['enabled'][] = $entity;
      }
      else {
        $entities['disabled'][] = $entity;
      }
    }

    return $entities;
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $entities = $this->load();

    $build['#type'] = 'container';

    $build['enabled']['heading']['#markup'] = '<h2>' . $this->t('Enabled') . '</h2>';
    $build['disabled']['heading']['#markup'] = '<h2>' . $this->t('Disabled') . '</h2>';
    foreach (['enabled', 'disabled'] as $status) {
      $build[$status]['#type'] = 'container';
      $build[$status]['table'] = [
        '#type' => 'table',
        '#header' => $this->buildHeader(),
        '#row' => [],
        '#cache' => [
          'contexts' => $this->entityType->getListCacheContexts(),
          'tags' => $this->entityType->getListCacheTags(),
        ],
      ];
      foreach ($entities[$status] as $entity) {
        if ($row = $this->buildRow($entity)) {
          $build[$status]['table']['#rows'][$entity->id()] = $row;
        }
      }
    }
    $build['enabled']['table']['#empty'] = $this->t('There are no enabled @label.', ['@label' => $this->entityType->getPluralLabel()]);
    $build['disabled']['table']['#empty'] = $this->t('There are no disabled @label.', ['@label' => $this->entityType->getPluralLabel()]);

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);

    $operations['person'] = [
      'title' => t('List'),
      'weight' => 0,
      'url' => $entity->toUrl('person'),
    ];

    return $operations;
  }

}
