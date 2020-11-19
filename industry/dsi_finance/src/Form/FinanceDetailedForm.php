<?php


namespace Drupal\dsi_finance\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FinanceDetailedForm extends ContentEntityForm {
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
    /* @var \Drupal\dsi_finance\Entity\FinanceDetailed $entity */
    $form = parent::buildForm($form, $form_state);
    $type = \Drupal::routeMatch()->getParameter('type');


    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    /**
     *   #input: array:10 [▼
    "name" => array:1 [▼
    0 => array:1 [▼
    "value" => "武侠剧投资项目"
    ]
    ]
     */
    $form_state->setValue('type','1'. $form_state->getValue('type'));
    
//        $form_state['#input']['type'][0]['value'] = 1;
//    dd($form_state);

    $status = parent::save($form, $form_state);
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