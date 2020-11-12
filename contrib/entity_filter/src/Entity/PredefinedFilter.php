<?php

namespace Drupal\entity_filter\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * 该类名存在问题，不仅仅是条件设置用该类，栏目设置也在用该类.
 *
 * @ConfigEntityType(
 *   id = "predefined_filter",
 *   label = @Translation("Predefined filter"),
 *   config_prefix = "predefined_filter",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "filters",
 *     "relationships",
 *   },
 * )
 */
class PredefinedFilter extends ConfigEntityBase implements PredefinedFilterInterface {

  /**
   * The Predefined filter ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Predefined filter label.
   *
   * @var string
   */
  protected $label;

  protected $filters = [];

  protected $relationships = [];

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return $this->filters;
  }

  /**
   * {@inheritdoc}
   */
  public function setFilters($filters) {
    $this->filters = $filters;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRelationships() {
    return $this->relationships;
  }

  /**
   * {@inheritdoc}
   */
  public function getRelationship($name) {
    return $this->relationships[$name];
  }

  /**
   * {@inheritdoc}
   */
  public function setRelationship($name, $relationship) {
    $this->relationships[$name] = $relationship;

    return $this;
  }

}
