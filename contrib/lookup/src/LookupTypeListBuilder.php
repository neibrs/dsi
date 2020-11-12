<?php

namespace Drupal\lookup;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Lookup type entities.
 */
class LookupTypeListBuilder extends ConfigEntityListBuilder {

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
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);

    if ($entity->access('view')) {
      $operations['list'] = [
        'title' => t('List'),
        'weight' => 0,
        'url' => $entity->toUrl('lookup'),
      ];
    }
    return $operations;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Lookup type');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    // You probably want a few more properties here...
    return $row + parent::buildRow($entity);
  }

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
        '#rows' => [],
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

}
