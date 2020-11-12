<?php

namespace Drupal\views_template\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\migrate\Plugin\migrate\process\Route;

/**
 * Class ViewTemplateForm.
 */
class ViewTemplateForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
  
    /** @var \Drupal\views_template\Entity\ViewTemplateInterface $view_template */
    $view_template = $this->entity;

    $view = $this->getRequest()->get('view');

    if ($view_template->isNew()) {
      if (empty($view)) {
        // TODO
        return [];
      }
      $view_template->setViewId($view);
    }

    if ($view) {
      $overrides = \Drupal::service('views_template.manager')->getViewOverride($view);
      if (isset($overrides['filters'])) {
        $view_template->setFilters($overrides['filters']);
      }
      if (isset($overrides['fields'])) {
        $view_template->setFields($overrides['fields']);
      }
    }

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 32,
      '#default_value' => $view_template->label(),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $view_template->id(),
      '#machine_name' => [
        'exists' => '\Drupal\views_template\Entity\ViewTemplate::load',
      ],
      '#disabled' => !$view_template->isNew(),
    ];
  
    $form['is_public'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Is public'),
      '#default_value' => $view_template->getIsPublic(),
      '#access' => \Drupal::currentUser()->hasPermission('maintain public view templates'),
    ];

    if ($view && $view_template->isNew()) {
      $form['view_templates'] = [
        '#type' => 'table',
        '#header' => [$this->t('View template'), $this->t('Machine name'), $this->t('Operations')],
      ];
      $view_templates = \Drupal::entityTypeManager()->getStorage('view_template')->loadByProperties([
        'view_id' => $view,
        'user_id' => \Drupal::currentUser()->id(),
      ]);

      foreach ($view_templates as $id => $v) {
        $form['view_templates'][] = [
          'name' => ['#markup' => Link::createFromRoute(
            $v->label(),
            'entity.view_template.edit_form',
            [
              'view_template' => $id
            ],
            [
              'query' => [
                'view' => $view,
                'destination' => $this->getRequest()->get('destination')
              ]
            ]
          )->toString()],
          'id' => ['#markup' => $id],
          'operation' => $v->toLink('Delete', 'delete-form', [
            'query' => [
              \Drupal::destination()->getAsArray()
            ]
          ])->toRenderable(),
        ];
      }
    }


    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\views_template\Entity\ViewTemplateInterface $view_template */
    $view_template = $this->entity;
    
    $view_template->setUserId(\Drupal::currentUser()->id());
    
    $status = $view_template->save();
  
    \Drupal::service('views_template.manager')->setViewTemplate($view_template->getViewId(), $view_template->id());

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label View template.', [
          '%label' => $view_template->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label View template.', [
          '%label' => $view_template->label(),
        ]));
    }
    $form_state->setRedirectUrl($view_template->toUrl('collection'));
  }

}
