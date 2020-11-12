<?php

namespace Drupal\organization_hierarchy\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_plus\Entity\ContentEntityForm;

/**
 * Form controller for Organization hierarchy edit forms.
 *
 * @ingroup organization_hierarchy
 */
class OrganizationHierarchyForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\organization_hierarchy\Entity\OrganizationHierarchy */
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;

    $entities = \Drupal::entityTypeManager()->getStorage('lookup')->loadByProperties([
      'type' => 'organization_hierarchy_type',
    ]);
    $options = [];
    foreach ($entities as $e) {
      $name = $e->name->value;
      $options[$name] = $name;
    }

    $form['name']['widget'][0]['value']['#type'] = 'select';
    $form['name']['widget'][0]['value']['#options'] = $options;
    $form['name']['widget'][0]['value']['#multiple'] = FALSE;
    unset($form['name']['widget'][0]['value']['#size']);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    parent::save($form, $form_state);

    $form_state->setRedirect('entity.organization_hierarchy.canonical', ['organization_hierarchy' => $entity->id()]);
  }

}
