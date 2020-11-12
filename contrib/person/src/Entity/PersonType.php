<?php

namespace Drupal\person\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Defines the Person type entity.
 *
 * @ConfigEntityType(
 *   id = "person_type",
 *   label = @Translation("Person type"),
 *   label_collection = @Translation("Person types"),
 *   handlers = {
 *     "view_builder" = "Drupal\person\PersonTypeViewBuilder",
 *     "list_builder" = "Drupal\person\PersonTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\person\Form\PersonTypeForm",
 *       "edit" = "Drupal\person\Form\PersonTypeForm",
 *       "delete" = "Drupal\eabax_core\Form\BundleDeleteForm",
 *     },
 *     "access" = "Drupal\person\PersonTypeAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\person\PersonTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "type",
 *   admin_permission = "administer persons",
 *   bundle_of = "person",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/person/type/{person_type}",
 *     "add-form" = "/person/type/add",
 *     "edit-form" = "/person/type/{person_type}/edit",
 *     "delete-form" = "/person/type/{person_type}/delete",
 *     "collection" = "/person/type",
 *     "person" = "/person/type/{person_type}/person",
 *     "enable" = "/person/type/{person_type}/enable",
 *     "disable" = "/person/type/{person_type}/disable",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "alias",
 *     "system_type",
 *     "is_system",
 *     "is_employee",
 *   }
 * )
 */
class PersonType extends ConfigEntityBundleBase implements PersonTypeInterface {

  /**
   * The Person type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Person type label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Person type label alias.
   * @var string
   */
  protected $alias;

  /**
   * The system type.
   *
   * @var string
   */
  protected $system_type;

  /**
   * System person type.
   *
   * @var boolean
   */
  protected $is_system = FALSE;

  /**
   * {@inheritdoc}
   */
  public function isLocked() {
    $locked = \Drupal::state()->get('person.type.locked');
    return isset($locked[$this->id()]) ? $locked[$this->id()] : FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public static function sort(ConfigEntityInterface $a, ConfigEntityInterface $b) {
    $a_weight = isset($a->weight) ? $a->weight : 0;
    $b_weight = isset($b->weight) ? $b->weight : 0;
    if ($a_weight == $b_weight) {
      // 根据系统人员类型排序.
      $a_label = $a->getSystemType();
      $b_label = $b->getSystemType();
      return strnatcasecmp($a_label, $b_label);
    }
    return ($a_weight < $b_weight) ? -1 : 1;
  }

  /**
   * {@inheritdoc}
   */
  public function getSystemType() {
    return $this->system_type;
  }

  /**
   * {@inheritdoc}
   */
  public function getIsSystem() {
    return $this->is_system;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmployee() {
    return $this->is_employee;
  }

  public function loadAllChildren() {
    $children = $this->loadChildren();

    foreach ($children as $child) {
      $children += $child->loadChildren();
    }

    return $children;
  }

  public function loadChildren() {
    $storage = $this->entityTypeManager()->getStorage('person_type');
    $query = $storage->getQuery();
    $query->condition('system_type', $this->id());
    $ids = $query->execute();

    return $storage->loadMultiple($ids);
  }

  public function getAlias() {
    return $this->alias;
  }

  public function setAlias($alias) {
    $this->alias = $alias;
    return $this;
  }
}
