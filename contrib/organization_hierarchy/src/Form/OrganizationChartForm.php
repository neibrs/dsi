<?php

namespace Drupal\organization_hierarchy\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\organization\Entity\OrganizationInterface;
use Drupal\organization\Form\OrganizationChartForm as OrganizationChartFormBase;

class OrganizationChartForm extends OrganizationChartFormBase {

  /**
   * {@inheritdoc}
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

    $options = [];
    $entities = \Drupal::entityTypeManager()->getStorage('lookup')->loadByProperties([
      'type' => 'organization_hierarchy_type',
    ]);
    foreach ($entities as $e) {
      $name = $e->name->value;
      $options[$name] = $name;
    }

    $form['organization_hierarchy_switch'] = [
      '#type' => 'select',
      '#title' => '组织层级选择',
      '#options' => $options,
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

    $organization_hierarchy = null;
    if ($form_state->hasValue('organization_hierarchy_switch')) {
      $organization_hierarchy = $form_state->getValue('organization_hierarchy_switch');
    }

    $max_depth = 0;
    $items = $this->getTreeItem($organization, $show_manager, $show_holder_count, $organization_hierarchy, $max_depth);
    
    // 层级展示选择
    $i = 0;
    $options = [];
    while ($i < $max_depth) {
      $options[$i] = $i;
      $i++;
    }
    $form['hierarchy_expansion_switch'] = [
      '#type' => 'select',
      '#title' => '层级展开',
      '#options' => $options,
      '#default_value' => $max_depth > 1 ? 1 : 0,
      '#ajax' => [
        'callback' => '::updateChart',
        'wrapper' => 'organization-chart-wrapper',
      ],
    ];
    $hierarchy_expansion = 1;
    if ($form_state->hasValue('hierarchy_expansion_switch')) {
      $hierarchy_expansion = $form_state->getValue('hierarchy_expansion_switch');
    }

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
    $form['chart']['#attributes']['data-hierarchy-expansion'] = $hierarchy_expansion;
  
    $form['chart']['#cache']['tags'] = \Drupal::entityTypeManager()->getDefinition('organization')->getListCacheTags();

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getTreeItem(OrganizationInterface $organization, $show_manager, $show_holder_count, $organization_hierarchy = NULL, &$max_depth = NULL) {
    $item = [
      'name' => $organization->label(),
      'url' => 'organization/'. $organization->id(),
    ];

    $max_depth += 1;
    
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

    $entityTypeManager = \Drupal::entityTypeManager();
    $children = [];
    if (!empty($organization_hierarchy)) {
       $organization_hierarchy_entities = $entityTypeManager->getStorage('organization_hierarchy')->loadByProperties([
         'name' => $organization_hierarchy,
         'organization' => $organization->id(),
       ]);
       if (!empty($organization_hierarchy_entities)) {
         $subordinates = reset($organization_hierarchy_entities)->subordinates->getValue();
         $subordinates_ids = array_map(function ($items) {
           return $items['target_id'];
         }, $subordinates);

         $children = $entityTypeManager->getStorage('organization')->loadMultiple($subordinates_ids);
       }
    }
    else {
      $children = $entityTypeManager->getStorage('organization')->loadByProperties([
        'parent' => $organization->id(),
      ]);
    }
    $child_max_depth = 0;
    foreach ($children as $child) {
      $child_depth = 0;
      /** @var \Drupal\organization\Entity\OrganizationInterface $child */
      $below[] = $this->getTreeItem($child, $show_manager, $show_holder_count, $organization_hierarchy, $child_depth);
      
      if ($child_depth > $child_max_depth) {
        $child_max_depth = $child_depth;
      }
    }
    if (isset($below)) {
      $item['children'] = $below;
    }
    
    $max_depth += $child_max_depth;

    return $item;

  }

}