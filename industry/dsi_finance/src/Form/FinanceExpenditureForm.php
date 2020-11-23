<?php


namespace Drupal\dsi_finance\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
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
        /* @var \Drupal\dsi_finance\Entity\FinanceExpenditure $entity */
        $form = parent::buildForm($form, $form_state);
      $form['relation_type']['widget']['#ajax'] = [
        'callback' => '::ajaxChangeRelationDataCallback',
        'wrapper' => 'edit-relation',
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
    /** @var \Drupal\Core\Entity\EntityStorageInterface $lookup_storage */
    $lookup_storage = \Drupal::service('entity_type.manager')->getStorage('lookup');
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

    /**
     * {@inheritdoc}
     */
    public function save(array $form, FormStateInterface $form_state)
    {
        $entity = $this->entity;
        $status = parent::save($form, $form_state);
        if ($status) {
            $database = \Drupal::database();
          $id = $entity->id();
          $financeExpenditure = $entity->toArray();
          if ($entity->isNew()) {
                //添加支出明细记录
                $database->insert('dsi_finance_detailed_field_data')
                    ->fields(
                        [
                            'type' => 2,//支出
                            'name' => $financeExpenditure['name'][0]['value'],
                            'price' => $financeExpenditure['price'][0]['value'],
                            'happen_date' => $financeExpenditure['happen_date'][0]['value'],
                            'happen_by' => $financeExpenditure['happen_by'][0]['value'],
                            'cases' => $financeExpenditure['cases'][0]['value'],
                            'finance_id' => $id,
                        ]
                    )
                    ->execute();
            } else {
                //更新支出明细记录
                $database->update('dsi_finance_detailed_field_data')
                    ->fields(
                        [
                          'name' => $financeExpenditure['name'][0]['value'],
                          'price' => $financeExpenditure['price'][0]['value'],
                          'happen_date' => $financeExpenditure['happen_date'][0]['value'],
                          'happen_by' => $financeExpenditure['happen_by'][0]['value'],
                          'cases' => $financeExpenditure['cases'][0]['value'],
                          'finance_id' => $id,
                        ]
                    )
                    ->condition('finance_id', $id)
                    ->execute();
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