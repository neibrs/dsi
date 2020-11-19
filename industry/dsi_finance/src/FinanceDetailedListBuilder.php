<?php

namespace Drupal\dsi_finance;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

class FinanceDetailedListBuilder extends EntityListBuilder {
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('FinanceDetailed ID');
    $header['name'] = $this->t('Name');
    $header['price'] = $this->t('Price');
    $header['happen_date'] = $this->t('Happen Date');
    $header['happen_by'] = $this->t('Happen By');
    $header['cases'] = $this->t('Cases');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\dsi_finance\Entity\FinanceDetailed $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.dsi_finance_detailed.edit_form',
      ['dsi_finance_detailed' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }
}