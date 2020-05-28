<?php

namespace Drupal\entity_filter\Controller;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\SettingsCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormState;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\views\ViewExecutable;
use Drupal\views\Views;

class EntityFilterController extends ControllerBase {

  /**
   * 条件设置的自定义字段设置AJAX处理： /entity_filter/views_handler_config/{base_table}/{handler_type}/{key}
   */
  public function viewsHandlerConfig($base_table, $handler_type, $key) {
    // Build form from plugin's buildOptionsForm.
    list($table, $field) = explode('.', $key);

    $handler = Views::handlerManager($handler_type)->getHandler([
      'table' => $table,
      'field' => $field,
    ]);

    // init的作用：设置Drupal\views\Plugin\views\filter\Bundle对象的entityType属性。
    $view = \Drupal::entityTypeManager()->getStorage('view')->create([
      'base_table' => $table,
    ]);
    $view_executable = Views::executableFactory()->get($view);
    $display = $view_executable->getDisplay();
    $handler->init($view_executable, $display);

    $types = ViewExecutable::getHandlerTypes();
    $form = [
      'options' => [
        '#tree' => TRUE,
        '#theme_wrappers' => ['container'],
        '#attributes' => ['class' => ['scroll'], 'data-drupal-views-scroll' => TRUE],
      ],
    ];

    // Relationships
    $relationships = [];
    if ($predefined_filter = \Drupal::entityTypeManager()->getStorage('predefined_filter')->load($base_table)) {
      $relationships = $predefined_filter->getRelationships();
    }

    $relationship_options = [];
    foreach ($relationships as $relationship) {
      $relationship_handler = Views::handlerManager('relationship')->getHandler($relationship);
      // ignore invalid/broken relationships.
      if (empty($relationship_handler)) {
        continue;
      }

      // If this relationship is valid for this type, add it to the list.
      $data = Views::viewsData()->get($relationship['table']);
      if (isset($data[$relationship['field']]['relationship']['base']) && $base = $data[$relationship['field']]['relationship']['base']) {
        $base_fields = Views::viewsDataHelper()->fetchFields($base, $handler_type);
        if (isset($base_fields[$key])) {
          $id = isset($relationship['id']) ? $relationship['id'] : $relationship['field'];
          $relationship_options[$id] = $relationship_handler->adminLabel();
        }
      }
    }

    if (!empty($relationship_options)) {
      $form['options']['relationship'] = [
        '#type' => 'select',
        '#title' => $this->t('Relationship'),
        '#options' => $relationship_options,
        '#weight' => -500,
      ];
    }
    else {
      $form['options']['relationship'] = [
        '#type' => 'value',
        '#value' => 'none',
      ];
    }

    // Build the form.
    $form_state = new FormState();

    // Fix Argument 3 passed to Drupal\Core\Entity\Element\EntityAutocomplete::processEntityAutocomplete() must be of the type array.
    $complete_form = [];
    $form_state->setCompleteForm($complete_form);

    $handler->buildOptionsForm($form['options'], $form_state);

    $response = new AjaxResponse();

    $step3 = [
      '#type' => 'container',
      '#attributes' => ['id' => 'custom_condition_step3'],
    ];

    unset($form['options']['expose_button']);
    unset($form['options']['more']);

    $step3['title'] = [
      '#markup' => $this->t('Configure @type: @item', ['@type' => $types[$handler_type]['lstitle'], '@item' => $handler->adminLabel()]),
      '#theme_wrappers' => ['container'],
      '#attributes' => ['id' => 'custom_condition_step3_title'],
    ];

    // Add #name property for $form elements
    $step3['options'] = \Drupal::formBuilder()->doBuildForm(NULL, $form, $form_state);
    $step3['options']['options']['#attributes']['data-plugin-id'] = $handler->getPluginId();

    $step3['save'] = [
      '#type' => 'link',
      '#title' => $this->t('Save'),
      '#url' => Url::fromRoute('<none>'),
      '#attributes' => [
        'id' => 'custom_condition_step3_save',
      ],
    ];
    $step3['return'] = [
      '#type' => 'link',
      '#title' => $this->t('Return'),
      '#url' => Url::fromRoute('<none>'),
      '#attributes' => [
        'id' => 'custom_condition_step3_return',
      ],
    ];
    $step3['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Cancel'),
      '#url' => Url::fromRoute('<none>'),
      '#attributes' => [
        'id' => 'custom_condition_step3_cancel',
      ],
    ];

    // Plugin options.
    $title = $handler->definition['title'];
    $settings['custom_conditions'] = [
      'plugin_options' => [
        'plugin_id' => $handler->getPluginId(),
        'table' => $table,
        'field' => $field,
        'field_title' => $title instanceof TranslatableMarkup ? $title->render() : $title,
      ],
    ];

    $response->addCommand(new CssCommand('#custom_condition_step1', ['display' => 'none']));
    $response->addCommand(new CssCommand('#custom_condition_step2', ['display' => 'none']));
    $response->addCommand(new ReplaceCommand('#custom_condition_step3', $step3));
    $response->addCommand(new SettingsCommand($settings, TRUE));

    return $response;
  }
}