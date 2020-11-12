<?php

namespace Drupal\report;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\views\Views;

/**
 * Provides a listing of Report entities.
 */
class ReportListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['category'] = $this->t('Category');
    $header['label'] = $this->t('Report', [], ['context' => 'Sheet']);
    $header['plugin'] = $this->t('Plugin');
    $header['base_table'] = $this->t('Base table');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\report\Entity\ReportInterface $entity */
    $row['category'] = $entity->getCategory();
    $row['label'] = $entity->toLink()->toString();
    $row['plugin'] = $entity->getPlugin()->getPluginDefinition()['label'];

    $views_data = Views::viewsData()->get($entity->get('settings')['base_table']);
    $row['base_table'] = $views_data['table']['base']['title'];
    return $row + parent::buildRow($entity);
  }

}
