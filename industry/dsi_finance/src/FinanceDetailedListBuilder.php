<?php

namespace Drupal\dsi_finance;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;
use Drupal\user\Entity\User;

class FinanceDetailedListBuilder extends EntityListBuilder {
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('FinanceDetailed ID');
    $header['type'] = $this->t('Type');
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
    $user = \Drupal::currentUser();
//    dd($user);

    $financeDetailed = $entity->toArray();
    $row['id'] = $entity->id();
    $row['type'] = $financeDetailed['type'][0]['value'] == 1?'收入':'支出';
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.dsi_finance_detailed.edit_form',
      ['dsi_finance_detailed' => $entity->id()]
    );
    $row['price'] = $financeDetailed['price'][0]['value'];
    $row['happen_date'] = $financeDetailed['happen_date'][0]['value'];
    $row['happen_by'] = $financeDetailed['happen_by'][0]['target_id'];
    $row['cases'] = $financeDetailed['cases'][0]['target_id'];

    return $row + parent::buildRow($entity);
  }
}