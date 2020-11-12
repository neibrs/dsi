<?php

namespace Drupal\person\Form;


use Drupal\Core\Form\FormStateInterface;
use Drupal\import\Form\ImportFormSelectSheet;

class PersonImportFormSelectSheet extends ImportFormSelectSheet {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $migration_id = NULL) {
    $form = parent::buildForm($form, $form_state, $migration_id);

    $lookup_storage = \Drupal::entityTypeManager()->getStorage('lookup');

    // 当选了表之后才处理.
    if (isset($this->column_map) && !empty($this->column_map)) {
      // 地址
      $entities = $lookup_storage->loadByProperties([
        'type' => 'person_address_type',
      ]);
      $this->addColumns($form, $this->t('Address'), $entities);

      // 电话
      $entities = $lookup_storage->loadByProperties([
        'type' => 'person_phone_type',
      ]);
      $this->addColumns($form, $this->t('Phones'), $entities);

      // 邮箱
      $entities = $lookup_storage->loadByProperties([
        'type' => 'person_email_type',
      ]);
      $this->addColumns($form, $this->t('Emails'), $entities);

      // 证件信息
      $entities = $lookup_storage->loadByProperties([
        'type' => 'identification_information_type',
      ]);
      $this->addColumns($form, $this->t('Identification information'), $entities);
    }

    return $form;
  }
  
  protected function addColumns(&$form, $title, $entities) {
    $row = [
      '#attributes' => ['class' => 'bg-blue']
    ];
    $row[] = [
      '#markup' => $title,
      '#wrapper_attributes' => ['colspan' => 2],
    ];
    $form['sources'][] = $row;
    foreach ($entities as $entity) {
      $row = [];
      $column_name = $entity->label();
      $row['source'] = ['#markup' => $column_name];
      $row['column'] = [
        '#type' => 'select',
        '#options' => $this->excel_columns,
        '#default_value' => $this->column_map[$column_name],
      ];
      $form['sources'][$column_name] = $row;
    }
  }

}