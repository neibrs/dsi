<?php

namespace Drupal\dsi_contract\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Contract edit forms.
 *
 * @ingroup dsi_contract
 */
class ContractForm extends ContentEntityForm {

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
    /* @var \Drupal\dsi_contract\Entity\Contract $entity */
    $form = parent::buildForm($form, $form_state);

    $client = \Drupal::routeMatch()->getParameter('dsi_client');
    if (!empty($client)) {
      $form['client']['widget'][0]['target_id']['#default_value'] = $client;
    }

    $form['person']['widget'][0]['target_id']['#default_value'] = \Drupal::service('person.manager')->currentPerson();

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
        $this->messenger()->addMessage($this->t('Created the %label Contract.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Contract.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.dsi_contract.canonical', ['dsi_contract' => $entity->id()]);
  }

}
