<?php

namespace Drupal\views_template\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\ViewEntityInterface;

class ViewTemplateSaveAsForm extends FormBase {
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'view_template_save_as_form';
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ViewEntityInterface $view = NULL) {
    if (empty($view)) {
      // TODO
      return [];
    }
    $this->overrides = \Drupal::service('views_template.manager')->getViewOverride($view);
    $this->view_id = $view->id();
    
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#maxlength' => 255,
      '#description' => $this->t("Label for the View template."),
      '#required' => TRUE,
    ];
    
    $form['id'] = [
      '#type' => 'machine_name',
      '#machine_name' => [
        'exists' => '\Drupal\views_template\Entity\ViewTemplate::load',
      ],
    ];
  
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save'),
      '#button_type' => 'primary',
    ];
    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $id = $form_state->getValue("id");
    $label = $form_state->getValue("label");
    
    $entity_storage = \Drupal::entityTypeManager()->getStorage('view_template');
    $entity = $entity_storage->create([
      'id' => $id,
      'label' => $label,
      'view_id' => $this->view_id,
      'filters' => $this->overrides['filters'],
      'fields' => $this->overrides['fields'],
      'user_id' => \Drupal::currentUser()->id(),
    ]);
    
    $entity->save();
    
    \Drupal::service('views_template.manager')->setViewTemplate($this->view_id, $entity->id());
  }
}