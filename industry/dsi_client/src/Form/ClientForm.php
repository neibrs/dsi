<?php

namespace Drupal\dsi_client\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
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

    $form['type']['widget']['#ajax'] = [
      'callback' => '::clientTypeSwitch',
      'wrapper' => 'client-type-wrapper',
    ];

    $form['client_type'] = [
      '#id' => 'client-type-wrapper',
      '#type' => 'inline_entity_form',
      '#entity_type' => 'crm_core_individual',
      '#bundle' => 'individual',
      '#form_mode' => 'normal',
    ];
    return $form;
  }

  /**
   * Handles switching the available regions based on the selected theme.
   */
  public function clientTypeSwitch($form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $type = $form_state->getValue('type')[0]['target_id'];
    $type = $this->entityTypeManager->getStorage('dsi_client_type')->load($type);

    $target_entity_type_id = $type->getTargetEntityTypeId();

    $form['client_type'] = [
      '#id' => 'client-type-wrapper',
      '#type' => 'inline_entity_form',
      '#entity_type' => $target_entity_type_id,
      '#bundle' => $type,
      '#form_mode' => 'normal',
    ];
    $response->addCommand(new ReplaceCommand('#client-type-wrapper', $form['client_type']));
    return $response;
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
