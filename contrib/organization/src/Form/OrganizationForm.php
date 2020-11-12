<?php

namespace Drupal\organization\Form;

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Organization edit forms.
 *
 * @ingroup organization
 */
class OrganizationForm extends BusinessGroupEntityForm {

  /**
   * {@inheritdoc}
   */
  public function prepareEntity() {
    parent::prepareEntity();

    if ($parent = $this->getRequest()->query->get('parent')) {
      $this->entity->parent->target_id = $parent;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $organization_entity = $this->entity;

    // 超级管理员可以添加顶层组织(business_group为空的组织)
    $user = \Drupal::currentUser();
    if (!$user->hasPermission('bypass_multiple_organization_access')) {
      $form['business_group']['widget']['#required'] = TRUE;
      unset($form['business_group']['widget']['#options']['_none']);
    }

    // 判断该组织是否使用，如果未使用才可以修改 classification
    if (!$organization_entity->isNew()) {
      $entity_types = \Drupal::entityTypeManager()->getDefinitions();
      foreach ($entity_types as $entity_type_id => $entity_type) {
        if ($multiple_organization_classification = $entity_type->get('multiple_organization_classification')) {
          $ids = \Drupal::entityTypeManager()->getStorage($entity_type_id)->getQuery()
            ->condition($multiple_organization_classification, $organization_entity->id())
            ->execute();
          if (!empty($ids)) {
            $form['classifications']['#disabled'] = TRUE;
            break;
          }
        }
      }
    }

    $form['currency']['#states'] = [
      'visible' => [
        'input[name="classifications[business_group]"' => ['checked' => TRUE],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;

    parent::save($form, $form_state);

    $form_state->setRedirect('entity.organization.canonical', ['organization' => $entity->id()]);
  }

}
