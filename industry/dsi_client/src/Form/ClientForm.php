<?php

namespace Drupal\dsi_client\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Client edit forms.
 *
 * @ingroup dsi_client
 */
class ClientForm extends ContentEntityForm {

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
    /* @var \Drupal\dsi_client\Entity\Client $entity */
    $form = parent::buildForm($form, $form_state);

//    if(!$this->entity->isNew() && $entity_type = $this->entity->get('entity_type')) {
//      $storage = $this->entityTypeManager->getStorage($entity_type);
//      if ($entity_id = $this->entity->get('entity_id')) {
//        $entity = $storage->load($entity_id);
//      }
//
//    }

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
        $this->messenger()->addMessage($this->t('Created the %label Client.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Client.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.dsi_client.canonical', ['dsi_client' => $entity->id()]);
  }

}