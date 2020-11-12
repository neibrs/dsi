<?php

namespace Drupal\user_plus\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\RoleInterface;

class UserRoleSwitchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return "user_role_switch_form";
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, RoleInterface $role = NULL) {
    $entities = \Drupal::entityTypeManager()->getStorage('user_role')
      ->loadMultiple();
    $options = array_map(function ($entity) {
      return $entity->label();
    }, $entities);
    $form['user_role'] = [
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $role->id(),
      '#attributes' => [
        'class' => ['select-submit'],
      ],
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['#attributes']['class'] = ['hidden'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Switch'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $route_match = \Drupal::routeMatch();

    $route_name = $route_match->getRouteName();
    $route_parameters = $route_match->getParameters();

    $route_parameters->set('user_role', $form_state->getValue('user_role'));

    $destination = [];
    $query = $this->getRequest()->query;
    if ($query->has('destination')) {
      $destination = ['destination' => $query->get('destination')];
      $query->remove('destination');
    }

    $form_state->setRedirect($route_name, $route_parameters->getIterator()->getArrayCopy(), $destination);
  }
}
