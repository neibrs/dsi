<?php

namespace Drupal\dsi_finance;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

class FinanceExpenditureListBuilder extends EntityListBuilder {
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Finance ID');
    $header['name'] = $this->t('Name');
    $header['price'] = $this->t('Receivable Price');
    $header['remarks'] = $this->t('Remarks');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\dsi_finance\Entity\FinanceExpenditure $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.dsi_finance_expenditure.edit_form',
      ['dsi_finance_expenditure' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }
}