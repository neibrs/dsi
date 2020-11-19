<?php


namespace Drupal\dsi_finance\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FinanceForm extends ContentEntityForm {
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
    /* @var \Drupal\dsi_finance\Entity\Finance $entity */
    $form = parent::buildForm($form, $form_state);
    $form['#attributes']['class'][0] = 'col s12';
    $form['name']['#attributes']['class'][0] = 'input-field col s6';
    unset($form['type']);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    //当前登录用户user_id
//    $form_state->getValues('#build_info')['user_id'][0]['target_id'];
    if (!empty($form_state->getValues('values')['type'])){
      switch ($form_state->getValues('values')['type'][0]['value']){
        case 1://收款
          $form_state->getValues('values')['type'][0]['value'] = 3;
          break;
        case 2://支出
          $form_state->getValues('values')['type'][0]['value'] = 4;
          break;
      }
    }
//    dd($form_state);
    $status = parent::save($form, $form_state);
    //添加收款 || 支出 明细记录
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