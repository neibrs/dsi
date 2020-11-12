<?php

namespace Drupal\organization\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\SettingsCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\organization\Entity\OrganizationInterface;

class OrganizationChartForm extends FormBase {

  /**
   */
  public function getFormId() {
    return 'organization_chart_form';
  }

  /**
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\person\Entity\PersonInterface $person */
    if (!$person = \Drupal::service('person.manager')->currentPerson()) {
      $form['description'] = ['#markup' => '帐号未关联人员，请联系管理员.'];
      return $form;
    }
    if (!$organization = $person->getOrganizationByClassification('business_group')) {
      $form['description'] = ['#markup' => '帐号关联人员不属于任何业务组，请联系管理员.'];
      return $form;
    }

    $form['container'] = [
      '#type' => 'container',
    ];
    $form['container']['#attributes']['class'][] = 'container-inline';

    $form['container']['orientation'] = [
      '#type' => 'radios',
      '#default_value' => 'horizontal',
      '#options' => [
        'vertical' => '纵向',
        'horizontal' => '横向',
      ],
      '#ajax' => [
        'callback' => '::updateChart',
        'wrapper' => 'organization-chart-wrapper',
      ],
    ];

    $form['container']['show_manager'] = [
      '#type' => 'checkbox',
      '#title' => '显示负责人',
      '#default_value' => FALSE,
      '#ajax' => [
        'callback' => '::updateChart',
        'wrapper' => 'organization-chart-wrapper',
      ],
    ];

    $form['container']['show_holder_count'] = [
      '#type' => 'checkbox',
      '#title' => '显示人数',
      '#default_value' => FALSE,
      '#ajax' => [
        'callback' => '::updateChart',
        'wrapper' => 'organization-chart-wrapper',
      ],
    ];

    $show_manager = FALSE;
    if ($form_state->hasValue('show_manager')) {
      $show_manager = $form_state->getValue('show_manager');
    }
    $show_holder_count = FALSE;
    if ($form_state->hasValue('show_holder_count')) {
      $show_holder_count = $form_state->getValue('show_holder_count');
    }
    $items = $this->getTreeItem($organization, $show_manager, $show_holder_count);

    $orientation = 'horizontal';
    if ($form_state->hasValue('orientation')) {
      $orientation = $form_state->getValue('orientation');
    }

    $form['chart'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'id' => 'tree-container',
      ],
      '#prefix' => '<div id="organization-chart-wrapper">',
      '#suffix' => '</div>',
      '#attached' => [
        'library' => [
          'organization/chart',
        ],
        'drupalSettings' => [
          'organizations' => $items,
        ],
      ],
    ];
    $form['chart']['#attributes']['data-orientation'] = $orientation;
    $form['chart']['#attributes']['data-show-manager'] = $show_manager;
    $form['chart']['#attributes']['data-show-holder-count'] = $show_holder_count;

    $form['chart']['#cache']['tags'] = \Drupal::entityTypeManager()->getDefinition('organization')->getListCacheTags();

    return $form;
  }

  protected function getTreeItem(OrganizationInterface $organization, $show_manager, $show_holder_count, $organization_hierarchy = NULL, &$max_depth = NULL) {
    //$organization = $this->entityRepository->getTranslationFromContext($organization);

    $item = [
      'name' => $organization->label(),
      'url' => 'organization/'. $organization->id(),
    ];

    // 获取组织 manager.
    if ($show_manager) {
      if (!empty($organization->manager->target_id)) {
        $item['manager'] = $organization->manager->entity->getName();
      }
      else {
        $item['manager'] = '';
      }
    }

    // 获取组织 holder_count
    if ($show_holder_count && \Drupal::moduleHandler()->moduleExists('employee_assignment')) {
      $query = \Drupal::database()->select('organization_employee_assignment_statistics', 'oeas');
      $query->condition('organization', $organization->id(), '=');
      $query->addField('oeas', 'holder_count');
      $holder_count = $query->execute()->fetchField();

      $item['holder_count'] = $holder_count ?: '';
    }

    $children = $organization->loadChildren(TRUE);
    foreach ($children as $child) {
      /** @var \Drupal\organization\Entity\OrganizationInterface $child */
      $below[] = $this->getTreeItem($child, $show_manager, $show_holder_count);
    }
    if (isset($below)) {
      $item['children'] = $below;
    }

    return $item;
  }

  /**
   * Ajax callback.
   */
  public function updateChart(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $settings = $form['chart']['#attached']['drupalSettings'];
    unset($form['chart']['#attached']['drupalSettings']);
    $response->addCommand(new SettingsCommand($settings));
    $response->addCommand(new ReplaceCommand('#organization-chart-wrapper', $form['chart']));

    return $response;
  }

  /**
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
  }
}
