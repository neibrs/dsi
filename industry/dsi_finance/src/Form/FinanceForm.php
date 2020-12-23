<?php


namespace Drupal\dsi_finance\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\dsi_finance\Entity\FinanceDetailed;
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
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\dsi_finance\Entity\Finance $entity */
    $form = parent::buildForm($form, $form_state);
    $form['relation_type']['widget']['#ajax'] = [
        'callback' => '::ajaxChangeRelationDataCallback', // 事件回调 方法 || 类 (名)
        'wrapper' => 'edit-relation', // Ajax需要改变元素id
    ];
    unset($form['relation_type']['widget']['#options']['_none']);
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state); // TODO: Change the autogenerated stub
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
    switch ($value){
      case '案件':
        $cases = $this->entityTypeManager->getStorage('dsi_cases')->loadMultiple();
        $casesData = $this->changeData($cases);
        $form['relation']['widget']['#options'] =  $this->changeRelationOptions($casesData);
        break;
      case '项目':
        $project = $this->entityTypeManager->getStorage('dsi_project')->loadMultiple();
        $projectData = $this->changeData($project);
        $form['relation']['widget']['#options'] =  $this->changeRelationOptions($projectData);
        break;
      case '客户':
        $client = $this->entityTypeManager->getStorage('dsi_client')->loadMultiple();
        $clientData = $this->changeData($client);
        $form['relation']['widget']['#options'] =  $this->changeRelationOptions($clientData);
        break;
    }
    $form['relation']['widget']['#id'] = 'edit-relation';
    return $form['relation'];
    }

  public function changeData ($data) {
    $datas = [];
    foreach ($data as $key => $val){
      $datas[] = [
        'id'=>$val->get('id')->value,
        'name'=>$val->get('name')->value,
      ];
    }
    return $datas;
  }

  public function changeRelationOptions($data){
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
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $auth_id = \Drupal::currentUser()->id();
    if ($entity->isNew() ) {
      //初始化待收金额
      $this->entity->set('wait_price', $form_state->getValue('receivable_price'));
    }

    $status = parent::save($form, $form_state);
    //更新财务详情部分字段
//      $database = \Drupal::database();
    //查询实体关联
    $finance_id = $entity->id();
    $finance_detail = $this->entityTypeManager
      ->getStorage('dsi_finance_detailed')
      ->loadByProperties([
        'entity_id' => $finance_id,
      ]);
//      $finance_detail = $database->query("select detail_target_id from dsi_finance__detail where entity_id = $finance_id")->fetchAll();
    $detailIds = [];
    foreach ($finance_detail as $key => $val ){
      $detailIds [] = $val->get('detail_target_id')->value;
    }
//      $ids = implode(",",$detailIds);
    $details = $this->entityTypeManager
      ->getStorage('dsi_finance_detailed')
      ->loadByProperties([
        'id' => $detailIds,
      ]);
//      $details = $database->query("select id,price,collection_date from dsi_finance_detailed_field_data where id in ($ids)")->fetchAll();
    $detailsData = [];
    $price = 0;
    foreach ($details as $key => $val) {
      $detailsData[$val->id] = $val->get('collection_date')->value;
      $price += $val->get('price')->value;
    }
    foreach ($detailIds as $key => $val ){
      $detail = FinanceDetailed::load($val);
      $detail
        ->set('finance_id',$entity->id())
        ->set('type',1)
        ->set('relation_type',$entity->get('relation_type')->getValue()[0]['target_id'])
        ->set('name',$entity->get('name')->getValue()[0]['value'])
        ->set('happen_date',empty($detailsData[$val])?'':$detailsData[$val])
        ->set('happen_by',$auth_id)
        ->set('relation',$entity->get('relation')->getValue()[0]['target_id'])
        ->save();
    }
    $finance = $this->entityTypeManager
      ->getStorage('dsi_finance_detailed')
      ->load($finance_id);
//      $finance = $database->query("select receivable_price,received_price from dsi_finance_field_data where id = $finance_id")->fetch();
    //本次修改 修改了已收款总额
    if ($finance->get('received_price')->value != $price) {
      //更新最近收款总额到财务表
      $wait_price = $finance->get('receivable_price')->value - $price;
      $wait_price = $wait_price < 0 ? 0 : $wait_price;
      $entity->received_price = $price;
      $entity->wait_price = $wait_price;
      $entity->save();
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