<?php

namespace Drupal\dsi_finance;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

class FinanceListBuilder extends EntityListBuilder {
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Finance ID');
    $header['name'] = $this->t('Name');
    $header['receivable_price'] = $this->t('Receivable Price');
    $header['received_price'] = $this->t('Received Price');
    $header['wait_price'] = $this->t('Wait Price');
    $header['appointment_time'] = $this->t('Appointment  Time');
    $header['remarks'] = $this->t('Remarks');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\dsi_finance\Entity\Finance $entity */
    $row['id'] = $entity->id();
//    $row['receivable_price'] = $entity->getFieldDefinition('receivable_price');
//    $row['receivable_price'] = $entity->get('receivable_price');
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.dsi_finance.edit_form',
      ['dsi_finance' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }
}