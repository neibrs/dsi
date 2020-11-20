<?php


namespace Drupal\dsi_finance\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FinanceExpenditureForm extends ContentEntityForm {
  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->account = $container->get('current_user');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\dsi_finance\Entity\FinanceExpenditure $entity */
    $form = parent::buildForm($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = parent::save($form, $form_state);
    //添加支出明细记录
    //$entity->isNew()//判断更新还是添加
    /**
     *
     */

//    //实体id
//    //更新收款实体 实收金额
//    $database = \Drupal::database();
//    $finance = $database->query('select receivable_price,received_price from dsi_finance_field_data')->fetch();
//    $received_price = 0;
//    $form_price = $form_state->getValue('price')[0]['value'];
//    if ($finance->received_price != 0){
//      $received_price += $form_price;
//    }else{
//      $received_price = $form_price;
//    }
//    $wait_price = $finance->receivable_price - $received_price;
//    $wait_price = $wait_price < 0 ? 0 : $wait_price;
//    $database
//      ->update('dsi_finance_field_data')
//      ->fields([
//        'received_price' => $received_price,
//        'wait_price' => $wait_price,
//      ])
//      ->condition('id', $finance_id)
//      ->execute();
    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Finance.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Finance.', [
          '%label' => $entity->label(),
        ]));
    }
//    $form_state->setRedirect('entity.dsi_finance.canonical');
    $form_state->setRedirect('entity.dsi_finance_expenditure.canonical', ['dsi_finance_expenditure' => $entity->id()]);
  }
}