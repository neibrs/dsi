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
      $detailed = $form_state->getValue('detailed');
      $database = \Drupal::database();

      if ($entity->isNew() ) {
        //初始化待收金额
        $this->entity->set('wait_price', $form_state->getValue('receivable_price'));
        //检测有没收款记录
        if (count($detailed)) {
          //添加收款记录
          $detailed = [
            [
              'price' => 1,
              'collection_date' => date('Y-m-d'),
              'invoice_code' => 1,
              'invoice_price' => 1,
              'invoice_date' => date('Y-m-d'),
            ],
            [
              'price' => 1,
              'collection_date' => date('Y-m-d'),
              'invoice_code' => 1,
              'invoice_price' => 1,
              'invoice_date' => date('Y-m-d'),
            ],
          ];
          foreach ($detailed as $k => $v) {
            //insert收款明细记录
            $database->insert('dsi_finance_detailed_field_data')
              ->fields([
                'price' => $v['price'],
                'collection_date' => $v['collection_date'],
                'invoice_code' => $v['invoice_code'],
                'invoice_price' => $v['invoice_price'],
                'invoice_date' => $v['invoice_date'],
              ])
              ->execute();
          }
        }
//dd(222);
      } else {

        $finance_id = $form_state->getValue('finance_id');

        if (count($detailed)){
          //更新收款记录
          $detailed = [
            [
              'id' => 0,
              'price' => 1,
              'collection_date' => date('Y-m-d'),
              'invoice_code' => 1,
              'invoice_price' => 1,
              'invoice_date' => date('Y-m-d'),
            ],
            [
              'id' => 1,
              'price' => 1,
              'collection_date' => date('Y-m-d'),
              'invoice_code' => 1,
              'invoice_price' => 1,
              'invoice_date' => date('Y-m-d'),
            ],
          ];
          //已收款总金额
          $price = 0;
          foreach ($detailed as $k => $v) {
            $price += $v['price'];
            if (!empty($v['id'])){//本次修改記錄
              $database
                ->update('dsi_finance_detailed_field_data')
                ->fields([
                  'collection_date' => $v['collection_date'],
                  'price' => $v['price'],
                  'invoice_date' => $v['invoice_date'],
                  'invoice_price' => $v['invoice_price'],
                  'invoice_code' => $v['invoice_code'],
                ])
                ->condition('id', $v['id'])
                ->execute();
            }else{//本次新增记录
              $database
                ->insert('dsi_finance_detailed_field_data')
                ->fields([
                  'collection_date' => $v['collection_date'],
                  'price' => $v['price'],
                  'invoice_date' => $v['invoice_date'],
                  'invoice_price' => $v['invoice_price'],
                  'invoice_code' => $v['invoice_code'],
                ])
                ->execute();
            }
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
              ->condition('id', $finance_id)
              ->execute();
          }
        }
      }

      $status = parent::save($form, $form_state);
//      dd(2121);
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
//      dd(2121,SAVED_NEW,77777777,$entity->id());

      $form_state->setRedirect('entity.dsi_finance.canonical', ['dsi_finance' => $entity->id()]);
    }
}