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
    $finance = $entity->toArray();
//    dd($entity->id());
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.dsi_finance.edit_form',
      ['dsi_finance' => $entity->id()]
    );
    $row['receivable_price'] = $finance['receivable_price'][0]['value'];
    $row['received_price'] = $finance['received_price'][0]['value'];
    $row['wait_price'] = $finance['wait_price'][0]['value'];
    $row['appointment_time'] = $finance['appointment_time'][0]['value'];
    $row['remarks'] = $finance['remarks'][0]['value'];
    //获取当前收款detailed记录集
//    $database = \Drupal::database();
//    $finance_id = $entity->id();
//    $row['detailed'] = $database->query("select price,collection_date,invoice_date,invoice_price,invoice_code from dsi_finance_detailed_field_data where finance_id = $finance_id")->fetchAll();
//    dd($row);
    return $row + parent::buildRow($entity);
  }
}