<?php

namespace Drupal\person\Plugin\views\filter;

use Drupal\Core\Database\Query\Condition;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\filter\BooleanOperator;
use Drupal\views\ViewExecutable;

/**
 * @ViewsFilter("current_person")
 */
class CurrentPerson extends BooleanOperator {

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);

    $this->value_value = $this->t('Is the logged in person');
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->ensureMyTable();

    $field = $this->tableAlias . '.' . $this->realField . ' ';
    $or = new Condition('OR');

    if (empty($this->value)) {
      $or->condition($field, '***CURRENT_PERSON***', '<>');
      if ($this->accept_null) {
        $or->isNull($field);
      }
    }
    else {
      $or->condition($field, '***CURRENT_PERSON***', '=');
    }
    $this->query->addWhere($this->options['group'], $or);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    $contexts = parent::getCacheContexts();

    // This filter depends on the current user.
    $contexts[] = 'user';

    return $contexts;
  }

}
