<?php


namespace Drupal\dsi_finance\Form;

use Drupal\Console\Bootstrap\Drupal;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FinanceDetailedForm extends ContentEntityForm
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
        /* @var \Drupal\dsi_finance\Entity\FinanceDetailed $entity */
        $form = parent::buildForm($form, $form_state);
        $name = \Drupal::routeMatch()->getParameter('finance_name');
        $finance_id = \Drupal::routeMatch()->getParameter('finance_id');
        $form['finance_id'] = array(
            '#type' => 'hidden',
            '#value' => $finance_id,
        );
        if ($this->entity->isNew()) {
            $user = \Drupal::currentUser();
            $form['type'] = array(
                '#type' => 'hidden',
                '#value' => 1,
            );
            $form['name'] = array(
                '#type' => 'hidden',
                '#value' => $name,
            );
            $form['happen_date'] = array(
                '#type' => 'hidden',
                '#value' => date('Y-m-d'),
            );
            $form['happen_by'] = array(
                '#type' => 'hidden',
                '#value' => $user->id(),
            );
        }

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $form, FormStateInterface $form_state)
    {
        /**
         *
         * //实例化数据库
         * //    $database = \Drupal::database();
         * //添加
         *    $result = $database->insert('mytable')
         * //      ->fields([
         * //        'title' => 'Example',
         * //        'uid' => 1,
         * //        'created' => \Drupal::time()->getRequestTime(),
         * //      ])
         * //      ->execute();
         * //查询
         * //    $finance = $database->query('select receivable_price,received_price from dsi_finance_field_data')->fetch();
         * //    $received_price = 0;
         * //    $form_price = $form_state->getValue('price')[0]['value'];
         * //    if ($finance->received_price != 0){
         * //      $received_price += $form_price;
         * //    }else{
         * //      $received_price = $form_price;
         * //    }
         * //    $wait_price = $finance->receivable_price - $received_price;
         * //    $wait_price = $wait_price < 0 ? 0 : $wait_price;
         * //更新
         * //    $database
         * //      ->update('dsi_finance_field_data')
         * //      ->fields([
         * //        'received_price' => $received_price,
         * //        'wait_price' => $wait_price,
         * //      ])
         * //      ->condition('id', $finance_id) //==where()
         * //      ->execute();
         * //    /**
         * //     * 上面的示例等效于以下查询：
         * //    UPDATE {mytable} SET field1=5, field2=1 WHERE created >= 1221717405;
         * //
         *
         */

        $entity = $this->entity;
//        dd($form_state);
        $status = parent::save($form, $form_state);

        if ($status){
          $finance_id = $form_state->getValue('finance_id');
          //更新收款实体 实收金额
          $database = \Drupal::database();
          $finance = $database->query("select receivable_price,received_price from dsi_finance_field_data where id = $finance_id")->fetch();
          $detailed = $database->query("select price from dsi_finance_detailed_field_data where finance_id = $finance_id")->fetchAll();
          $price = 0;
          foreach ($detailed as $key => $val) {
            $price += $val->price;
          }
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
          /**
           * 上面的示例等效于以下查询：
           * UPDATE {mytable} SET field1=5, field2=1 WHERE created >= 1221717405;
           */
        }

        switch ($status) {
            case SAVED_NEW:
                $this->messenger()->addMessage($this->t('Created the %label FinanceDetailed.', [
                    '%label' => $entity->label(),
                ]));
                break;

            default:
                $this->messenger()->addMessage($this->t('Saved the %label FinanceDetailed.', [
                    '%label' => $entity->label(),
                ]));
        }
        $form_state->setRedirect('entity.dsi_finance_detailed.canonical', ['dsi_finance_detailed' => $entity->id()]);
    }
}