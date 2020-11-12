<?php

namespace Drupal\views_template\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormState;
use Drupal\Core\Url;
use Drupal\views\ViewEntityInterface;
use Drupal\views\ViewExecutable;
use Drupal\views\Views;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ViewsTemplateController extends ControllerBase {

  /**
   * 栏目设置：字段配置.
   *
   * @see \Drupal\views_ui\Form\Ajax\ConfigHandler::buildForm()
   */
  public function viewsFieldOption(ViewEntityInterface $view, $field_id) {
    module_load_include('inc', 'views_ui', 'admin');

    $executable = $view->getExecutable();
    $base_table = $view->get('base_table');

    list($table, $table_field) = explode('.', $field_id);
    $handler = Views::handlerManager('field')->getHandler([
      'table' => $table,
      'field' => $table_field,
    ]);

    $handler->init($executable, $executable->getDisplay());
    $form = [
      'options' => [
        '#tree' => TRUE,
        '#theme_wrappers' => ['container'],
        '#attributes' => ['class' => ['scroll'], 'data-drupal-views-scroll' => TRUE],
      ],
    ];

    // Build the form.
    $form_state = new FormState();

    // Fix Argument 3 passed to Drupal\Core\Entity\Element\EntityAutocomplete::processEntityAutocomplete() must be of the type array.
    $complete_form = [];
    $form_state->setCompleteForm($complete_form);

    /** @see views_ui_build_form_url() */
    $form_state->set('ajax', TRUE);
    $form_state->set('view', $view);
    $form_state->set('form_key', 'handler');
    $form_state->set('display_id', $executable->getDisplay()->display['id']);
    $form_state->set('type', 'field');
    $form_state->set('id', $field_id);
  
    // 将用户数据设置到 $form_state，因为 buildOptionsForm 在构造格式化器的配置表单时需要取 form_state 的 options 的 type.
    $options = \Drupal::request()->get('options');
    if ($options) {
      $form_state->set('options', $options);
      $form_state->setUserInput(['options' => $options]);
    }
    
    $handler->buildOptionsForm($form['options'], $form_state);
    
    // 修改格式化器（type）的#ajax
    $form['options']['type']['#ajax']['url'] = Url::fromRoute('views_template.fields_override_config', [
      'view' => $view->id(),
      'field_id' => $field_id,
    ]);
    // 为格式化器的配置表单设置默认值.
    if (isset($options)) {
      foreach ($options as $key => $value) {
        if (in_array($key, ['field_info'])) {
          continue;
        }
        if (is_array($value)) {
          foreach ($value as $key2 => $value2) {
            $form['options'][$key][$key2]['#default_value'] = $value2;
          }
        }
        else {
          $form['options'][$key]['#default_value'] = $value;
        }
      }
    }

    $response = new AjaxResponse();
    $option = [
      '#type' => 'container',
      '#attributes' => ['id' => 'field_option'],
    ];

    $types = ViewExecutable::getHandlerTypes();
    $option['title'] = [
      '#markup' => $this->t('Configure @type: @item', ['@type' => $types['field']['lstitle'], '@item' => $handler->adminLabel()]),
      '#theme_wrappers' => ['container'],
      '#attributes' => ['id' => 'field_option_title'],
    ];
  
    // 添加 field 配置需要的数据.
    $field_plugin_definition = $handler->definition;
    $form['options']['table'] = ['#type' => 'hidden', '#value' => $table];
    $form['options']['field'] = ['#type' => 'hidden', '#value' => $table_field];
    if (isset($field_plugin_definition['entity_type'])) {
      $form['options']['entity_type'] = ['#type' => 'hidden', '#value' => $field_plugin_definition['entity_type']];
    }
    $form['options']['entity_field'] = $field_plugin_definition['entity field'] ?: $field_plugin_definition['field_name'];
    $form['options']['plugin_id'] = ['#type' => 'hidden', '#value' => $handler->getPluginId()];

    // Add #name property for $form elements
    $option['options'] = \Drupal::formBuilder()->doBuildForm(NULL, $form, $form_state);
  
    // Relationships
    $relationships = \Drupal::service('entity_filter.manager')->fetchRelationships($base_table);

    foreach ($relationships as $key => $relationship) {
      list($relationship_table, $relationship_field) = explode('.', $key);
      // If this relationship is valid for this type, add it to the list.
      $data = Views::viewsData()->get($relationship_table);
      if (isset($data[$relationship_field]['relationship']['base']) && $base = $data[$relationship_field]['relationship']['base']) {
        $base_fields = Views::viewsDataHelper()->fetchFields($base, 'field');
        if (isset($base_fields[$table . '.' . $table_field])) {
          $relationship_options[$relationship_field] = $relationship['title'];
        }
      }
    }

    if (!empty($relationship_options)) {
      $base_fields = Views::viewsDataHelper()->fetchFields($base_table, 'field');
      if (isset($base_fields[$field_id])) {
        $relationship_options = array_merge(['none' => $this->t('Do not use a relationship')], $relationship_options);
      }
  
      $option['options']['options']['relationship'] = [
        '#type' => 'select',
        '#title' => $this->t('Relationship'),
        '#options' => $relationship_options,
        '#weight' => -500,
      ];
      
      if ($options) {
        $option['options']['options']['relationship']['#value'] = $options['field_relationship'];
      }
    }
    else {
      $option['options']['options']['relationship'] = [
        '#type' => 'value',
        '#value' => 'none',
      ];
    }
    $option['options']['options']['relationship']['#attributes'] = [
      'id' => 'field_relationship',
      'name' => 'options[relationship]',
    ];

    $option['options']['options']['#attributes']['field-id'] = $field_id;
    
    $response->addCommand(new InvokeCommand('#field_option_content', 'removeClass', ['hidden']));
    $response->addCommand(new InvokeCommand('#override-wrapper', 'addClass', ['hidden']));
    $response->addCommand(new ReplaceCommand('#field_option', $option));

    return $response;
  }

}
