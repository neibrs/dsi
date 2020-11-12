<?php

namespace Drupal\data_security\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Instance set edit forms.
 *
 * @ingroup data_security
 */
class InstanceSetForm extends ContentEntityForm {

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
    /* @var \Drupal\data_security\Entity\InstanceSet $entity */
    $entity = $this->entity;

    $form = parent::buildForm($form, $form_state);

    if ($entity->isNew()) {
      // 添加时实体id为空，编辑 predicate 会报错.
      unset($form['predicate']);
    }
    else {
      // 只有添加时才允许修改 entity_type.
      $form['entity_type']['#disabled'] = TRUE;
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Instance set.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Instance set.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.instance_set.canonical', ['instance_set' => $entity->id()]);
  }

}
