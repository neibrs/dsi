<?php

namespace Drupal\dsi_client\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use http\Url;
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

    $type = $this->entityTypeManager->getStorage('dsi_client_type')->load($this->entity->bundle());
    $target_entity_type_id = $type->getTargetEntityTypeId();
    if ($type = $form_state->getValue('type')) {
      $type = $type[0]['target_id'];
      $type = $this->entityTypeManager->getStorage('dsi_client_type')->load($type);
      $target_entity_type_id = $type->getTargetEntityTypeId();
      $form['client_type'] = [
        '#id' => 'client-type-wrapper',
        '#type' => 'inline_entity_form',
        '#entity_type' => $target_entity_type_id,
        '#bundle' => $type->id(),
        '#form_mode' => 'normal',
      ];
    }
    else {
      $form['client_type'] = [
        '#id' => 'client-type-wrapper',
        '#type' => 'inline_entity_form',
        '#entity_type' => $target_entity_type_id,
        '#bundle' => $this->entity->bundle(),
        '#form_mode' => 'normal',
      ];
    }

    return $form;
  }

  /**
   * Handles switching the available regions based on the selected theme.
   */
  public function clientTypeSwitch($form, FormStateInterface $form_state) {
    return $form['client_type'];
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $values = $form_state->getValue('client_type');
    $entity = $this->updateRelatedEntity($form_state->getValue('type')[0]['target_id'], $values);

    $this->entity->set('entity_id', $entity->id());
    $this->entity->set('entity_type', $form_state->getValue('type')[0]['target_id']);
    $this->entity->save();
  }

  /**
   * Callback for related entity, crm_core_individual, crm_core_organization
   * @param $type
   * @param $values
   */
  protected function updateRelatedEntity($type, $values) {
    $client_type = $this->entityTypeManager->getStorage('dsi_client_type')->load($type);
    $target_entity_type_id = $client_type->getTargetEntityTypeId();
    $items = [];
    foreach ($values as $key => $value) {
      $items[$key] = isset($value[0]['value']) ? $value[0]['value'] : '';
    }
    $entity = $this->entityTypeManager->getStorage($target_entity_type_id)
      ->create($items + ['type' => $type]);
    $entity->save();
    return $entity;
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
