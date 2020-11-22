<?php


namespace Drupal\dsi_finance\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
        if ($this->entity->isNew()) {
            $finance_id = \Drupal::routeMatch()->getParameter('dsi_finance_id');
            $form['finance_id'] = array(
                '#type' => 'hidden',
                '#value' => 0,
            );
        }
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $form, FormStateInterface $form_state)
    {
        $entity = $this->entity;
        $detailed = $form_state->getValue('detailed');
        if ($entity->isNew()) {
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
                    //未知insert写法
                }
            }
        } else {
            $finance_id = $form_state->getValue('finance_id');
            //更新收款记录
            $detailed = [
                [
                    'id' => 1,
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
                $database = \Drupal::database();
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
        $status = parent::save($form, $form_state);

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