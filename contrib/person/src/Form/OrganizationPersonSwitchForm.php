<?php

namespace Drupal\person\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\person\Entity\PersonInterface;

class OrganizationPersonSwitchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'organization_person_switch';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, PersonInterface $person = NULL) {
    if ($organization = $person->getOrganization()) {
      $entities = \Drupal::entityTypeManager()->getStorage('person')
        ->loadByProperties(['organization' => $organization->id()]);
      $options = array_map(function ($entity) {
        return $entity->label();
      }, $entities);
    }
    else {
      $options[$person->id()] = $person->label();
    }
    $form['person'] = [
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $person->id(),
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

    $route_parameters->set('person', $form_state->getValue('person'));

    $form_state->setRedirectUrl(Url::fromRoute($route_name, $route_parameters->getIterator()->getArrayCopy()));
  }
}
