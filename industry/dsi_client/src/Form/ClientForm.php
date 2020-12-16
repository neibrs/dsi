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
        '#form_mode' => 'client',
      ];
    }
    else {
      $form['client_type'] = [
        '#id' => 'client-type-wrapper',
        '#type' => 'inline_entity_form',
        '#entity_type' => $target_entity_type_id,
        '#bundle' => $this->entity->bundle(),
        '#form_mode' => 'client',
        '#default_value' => !empty($this->entity->get('entity_id')->target_id) ? $this->entity->get('entity_id')->entity : NULL,
      ];
    }

    $cooperating_state_options = $form['cooperating_state']['widget']['#options'];
    if (empty($cooperating_state_options['#default_value'])) {
      // Set default value.
      $index = array_search('潜在', $cooperating_state_options);
      $form['cooperating_state']['widget']['#default_value'] = $index;
    }

    $client_importance = $form['client_importance']['widget']['#options'];
    if (empty($client_importance['#default_value'])) {
      $index = array_search('一般', $client_importance);
      $form['client_importance']['widget']['#default_value'] = $index;
    }

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $values = $form_state->getValue('client_type');
    $entity = $this->updateRelatedEntity($form_state->getValue('type')[0]['target_id'], $values, $this->entity->get('entity_id')->target_id);

    $this->entity->set('entity_id', $entity->id());
    $this->entity->set('entity_type', $form_state->getValue('type')[0]['target_id']);
    $this->entity->set('name', $entity->label());
    $this->entity->save();
  }

  /**
   * Handles switching the available regions based on the selected theme.
   */
  public function clientTypeSwitch($form, FormStateInterface $form_state) {
    return $form['client_type'];
  }

  /**
   */
  protected function updateRelatedEntity($type, $values, $entity_id = NULL) {
    $client_type = $this->entityTypeManager->getStorage('dsi_client_type')->load($type);
    $target_entity_type_id = $client_type->getTargetEntityTypeId();
    $items = [];
    foreach ($values as $key => $value) {
      if ($key == 'gender' or $key == 'nationality' or $key == 'manager'){
        $items[$key] = isset($value[0]['target_id']) ? $value[0]['target_id'] : 0;
      }else{
        $items[$key] = isset($value[0]['value']) ? $value[0]['value'] : '';
      }
    }
    if (empty($items['name'])) {
      $items['name'] = $items['phone'];
    }
    $entity_storage = $this->entityTypeManager->getStorage($target_entity_type_id);
    if ($entity_id) {
        // 批量保存$items到entity_id, TODO
        $entity = $entity_storage->load($entity_id);
        foreach ($items as $key => $val) {
            $entity->set($key, $val);
          }
      $entity->save();
    }
    else {
        $entity = $entity_storage->create($items + ['type' => $type]);
        $entity->save();
    }
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
    $form_state->setRedirect('entity.dsi_client.collection');
  }

}
