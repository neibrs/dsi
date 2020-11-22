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
        return $form;
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
            if ($entity->isNew()) {
                //添加支出明细记录
                $id = $entity->id();

                $database->insert('dsi_finance_field_data')
                    ->fields(
                        [
                            'type' => 2,//支出
                            'name' => $entity->get('name'),
                            'price' => $entity->get('price'),
                            'happen_date' => $entity->get('happen_date'),
                            'happen_by' => $entity->get('user_id'),
                            'cases' => $entity->get('cases'),
                            'finance_id' => $id,
                        ]
                    )
                    ->execute();
            } else {
                //更新支出明细记录
                $id = $entity->id();
                $database->update('dsi_finance_field_data')
                    ->fields(
                        [
                            'type' => 2,//支出
                            'name' => $entity->get('name'),
                            'price' => $entity->get('price'),
                            'happen_date' => $entity->get('happen_date'),
                            'happen_by' => $entity->get('user_id'),
                            'cases' => $entity->get('cases'),
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
//    $form_state->setRedirect('entity.dsi_finance.canonical');
        $form_state->setRedirect('entity.dsi_finance_expenditure.canonical', ['dsi_finance_expenditure' => $entity->id()]);
    }
}