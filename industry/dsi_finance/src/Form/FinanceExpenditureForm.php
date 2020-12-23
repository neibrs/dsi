<?php


namespace Drupal\dsi_finance\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\dsi_finance\Entity\FinanceDetailed;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FinanceExpenditureForm extends ContentEntityForm
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
//        $this->entityRepository
      $form['relation']['widget']['#id'] = 'edit-relation-new';
      $form['relation_type']['widget']['#ajax'] = [
        'callback' => '::ajaxChangeRelationOptionsCallback',
        'wrapper' => 'edit-relation-new',
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
  public function ajaxChangeRelationOptionsCallback(array &$form, FormStateInterface $form_state){
    /** @var \Drupal\Core\Entity\EntityStorageInterface $lookup_storage */
    $lookup_storage = \Drupal::service('entity_type.manager')->getStorage('lookup');
    $relation_type = $lookup_storage->load($form_state->getValue('relation_type')[0]['target_id']);
    $value = $relation_type->label();
//    $database = \Drupal::database();
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
    $form['relation']['widget']['#id'] = 'edit-relation-new';
    return $form['relation'];
  }

  public function changeData ($data)
  {
    $datas = [];
    foreach ($data as $key => $val){
      $datas[] = [
        'id'=>$val->get('id')->value,
        'name'=>$val->get('name')->value,
      ];
    }
    return $datas;
  }

  /**
   * @param $data
   *
   * @return array
   */
  public function changeRelationOptions($data){
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
      $is_add = 0;
      if($entity->isNew()){
        $is_add = 1;
      }
      $status = parent::save($form, $form_state);
      $id = $entity->id();
      if ($status) {
        if ($is_add) {
          $data = [
            'type'=>2,//支出
            'name'=>$entity->get('name')->getValue()[0]['value'],
            'price'=>$entity->get('price')->getValue()[0]['value'],
            'happen_date'=>$entity->get('happen_date')->getValue()[0]['value'],
            'happen_by'=>$entity->get('user_id')->getValue()[0]['target_id'],
            'relation_type'=>$entity->get('relation_type')->getValue()[0]['target_id'],
            'relation'=>$entity->get('relation')->getValue()[0]['target_id'],
            'finance_id'=>$id,
          ];

          FinanceDetailed::create($data)->save();
          } else {
              //更新支出明细记录
          $detail = $this->entityTypeManager
            ->getStorage('dsi_finance_detailed')
            ->loadByProperties([
            'finance_id' => $id,
          ]);
          $detail[1]
            ->set('name',$entity->get('name')->getValue()[0]['value'])
            ->set('price',$entity->get('price')->getValue()[0]['value'])
            ->set('happen_date',$entity->get('happen_date')->getValue()[0]['value'])
            ->set('happen_by',$entity->get('user_id')->getValue()[0]['target_id'])
            ->set('relation_type',$entity->get('relation_type')->getValue()[0]['target_id'])
            ->set('relation',$entity->get('relation')->getValue()[0]['target_id'])
            ->save();
          }
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
    $form_state->setRedirect('entity.dsi_finance_expenditure.canonical', ['dsi_finance_expenditure' => $entity->id()]);
  }
}