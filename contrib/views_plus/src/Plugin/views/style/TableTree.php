<?php

namespace Drupal\views_plus\Plugin\views\style;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\Table;

/**
 * @ViewsStyle(
 *   id = "table_tree",
 *   title = @Translation("Table tree"),
 *   theme = "views_view_table",
 *   display_types = {"normal"}
 * )
 */
class TableTree extends Table {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['tree_column'] = ['default' => ''];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form['#theme'] = 'views_plus_style_plugin_table_tree';

    $columns = $this->sanitizeColumns($this->options['columns']);

    foreach ($columns as $field => $column) {
      $radio_id = Html::getUniqueId('edit-tree-column-' . $field);

      if (isset($this->options['tree_column'])) {
        $default = $this->options['tree_column'];
        if (!isset($columns[$default])) {
          $default = -1;
        }
      }
      else {
        $default = -1;
      }
      $form['info'][$field]['tree_column'] = [
        '#title' => $this->t('Tree'),
        '#title_display' => 'invisible',
        '#type' => 'radio',
        '#return_value' => $field,
        '#parents' => ['style_options', 'tree_column'],
        '#id' => $radio_id,
        '#attributes' => ['id' => $radio_id],
        '#default_value' => $default,
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();

    $build['#attached']['library'][] = 'views_plus/table_tree';

    return $build;
  }

}
