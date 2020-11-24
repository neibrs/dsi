<?php


namespace Drupal\dsi_finance\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\lookup\InstallHelper;

class FinanceForm extends ContentEntityForm
{
    /**
     * The current user account.
     *
     * @var \Drupal\Core\Session\AccountProxyInterface
     */
    protected $account;

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container)
    {
        // Instantiates this form class.
        $instance = parent::create($container);

        $instance->account = $container->get('current_user');
        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        /* @var \Drupal\dsi_finance\Entity\Finance $entity */
        $form = parent::buildForm($form, $form_state);
        $form['relation_type']['widget']['#ajax'] = [
            'callback' => '::ajaxChangeRelationDataCallback', // 事件回调 方法 || 类 (名)
            'wrapper' => 'edit-relation', // Ajax需要改变元素id
        ];
        unset($form['relation_type']['widget']['#options']['_none']);
        //动态添加div
//        $form['detailed'] = $form['remarks'];
//
//      $form['detailed']['widget']['#id'] = 'edit-detailed';
//      $form['detailed']['widget']['#parents'][0] = 'detailed';
//      $form['detailed']['widget']['#title'] = $this->t('Detailed');
//      $form['detailed']['#title'] = $this->t('Detailed');
//      $form['detailed']['widget']['#field_name'] = 'detailed';
//      $form['detailed']['#parents'][0] = 'detailed_wrapper';
//      dd($form);
      return $form;
    }




  /**
   * Ajax动态更新 关联类型
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return mixed
   */
    public function ajaxChangeRelationDataCallback(array &$form, FormStateInterface $form_state){
      //查询选项定义的值
      /** @var \Drupal\Core\Entity\EntityStorageInterface $lookup_storage */
      $lookup_storage = \Drupal::service('entity_type.manager')->getStorage('lookup');
      //获取所有选项
      //      $relation_types =  $lookup_storage->loadByProperties([//获取选项所有数据
      //        'type'=>'relation_type',
      //      ]);
      //获取单条选项
      $relation_type = $lookup_storage->load($form_state->getValue('relation_type')[0]['target_id']);
      $value = $relation_type->label();
      $database = \Drupal::database();
      switch ($value){
        case '案件':
          $cases = $database->query('select id,name from dsi_cases_field_data')->fetchAll();
          $form['relation']['widget']['#options'] =  $this->changeRelationData($cases);
          break;
        case '项目':
          $project = $database->query('select id,name from dsi_project_field_data')->fetchAll();
          $form['relation']['widget']['#options'] =  $this->changeRelationData($project);
          break;
        case '客户':
          $client = $database->query('select id,name from dsi_client_field_data')->fetchAll();
          $form['relation']['widget']['#options'] =  $this->changeRelationData($client);
          break;
      }
      $form['relation']['widget']['#id'] = 'edit-relation';
      return $form['relation'];
    }

    public function changeRelationData($data){
      $options = [];
      foreach ($data as $key => $val){
        $options = [
          $val->id => $this->t($val->name),
        ];
      }
      return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $form, FormStateInterface $form_state)
    {
      $entity = $this->entity;
      $auth_id = \Drupal::currentUser()->id();
      if ($entity->isNew() ) {
        //初始化待收金额
        $this->entity->set('wait_price', $form_state->getValue('receivable_price'));
      }

      $status = parent::save($form, $form_state);

      //更新财务详情部分字段
      $database = \Drupal::database();
      //查询实体关联
      $finance_id = $entity->id();
      $finance_detail = $database->query("select detail_target_id from dsi_finance__detail where entity_id = $finance_id")->fetchAll();
      $detailIds = [];
      foreach ($finance_detail as $key => $val ){
        $detailIds [] = $val->detail_target_id;
      }
      $ids = implode(",",$detailIds);
      $details = $database->query("select id,price,happen_date from dsi_finance_detailed_field_data where id in ($ids)")->fetchAll();
      $detailsData = [];
      $price = 0;
      foreach ($details as $key => $val) {
        $detailsData[$val->id] = $val->happen_date;
        $price += $val->price;
      }
      foreach ($detailIds as $key => $val ){
        $database->update('dsi_finance_detailed_field_data')
        ->fields([
          'finance_id'=>$entity->id(),
          'type'=>1,
          'name'=>$entity->get('name')->getValue()[0]['value'],
          'happen_date'=>empty($detailsData[$val['id']])?'':$detailsData[$val['id']],
          'happen_by'=>$auth_id,
          'relation'=>$entity->get('relation')->getValue()[0]['value'],
        ])
        ->condition('id',$val['id'])
        ->execute();
      }
      $finance = $database->query("select receivable_price,received_price from dsi_finance_field_data where id = $finance_id")->fetch();
      //本次修改 修改了已收款总额
      if ($finance->received_price != $price) {
        //更新最近收款总额到财务表
        $wait_price = $finance->receivable_price - $price;
        $wait_price = $wait_price < 0 ? 0 : $wait_price;
        $database
          ->update('dsi_finance_field_data')
          ->fields([
            'received_price' => $price,
            'wait_price' => $wait_price,
          ])
          ->condition('id', $entity->id())
          ->execute();
      }
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
      $form_state->setRedirect('entity.dsi_finance.canonical', ['dsi_finance' => $entity->id()]);
    }
}