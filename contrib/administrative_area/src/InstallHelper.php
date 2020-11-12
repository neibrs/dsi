<?php

namespace Drupal\administrative_area;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InstallHelper implements ContainerInjectionInterface {

  /**
 * @var \Drupal\Core\Entity\EntityTypeManagerInterface*/
  protected $entityTypeManager;

  /**
   * InstallHelper constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  public function installData() {
    // GB_T2260_2018 行政区划字典
    $this->installContentFromFile('administrative_area', 'administrative_area');
  }

  protected function installContentFromFile($entity_type, $bundle_machine_name) {
    $filename = $entity_type . '/' . $bundle_machine_name . '.csv';
    $all_content = $this->readContent($filename);

    $this->processContent($bundle_machine_name, $all_content);

    return $this;
  }

  protected function readContent($filename) {
    $default_content_path = drupal_get_path('module', 'administrative_area') . "/data";

    $keyed_content = [];
    if (file_exists($default_content_path . "/$filename") &&
      ($handle = fopen($default_content_path . "/$filename", 'r')) !== FALSE) {
      $header = fgetcsv($handle);
      $line_counter = 0;
      while (($content = fgetcsv($handle)) !== FALSE) {
        $keyed_content[$line_counter] = array_combine($header, $content);
        $line_counter++;
      }
      fclose($handle);
    }

    return $keyed_content;
  }

  protected function processContent($bundle_machine_name, array $content) {
    $structured_content = [];
    switch ($bundle_machine_name) {
      case 'administrative_area':
        $structured_content = $this->processLookup($content, $bundle_machine_name);
        break;

      default:
        break;
    }
    return $structured_content;
  }

  protected function processLookup(array $data, $bundle_machine_name) {
    $administrative_area_storage = $this->entityTypeManager->getStorage('administrative_area');

    foreach ($data as $id => $row) {
      $values = [
        'code' => $row['键'],
        'name' => $row['值'],
      ];

      $parent_code = NULL;
      $area_str = substr($values['code'], 2, 2);
      $loc_str = substr($values['code'], 4, 2);
      if ($area_str == '00' && $loc_str == '00') {
        $values['type'] = 'administrative_area';
      }
      elseif ($loc_str == '00') {
        $values['type'] = 'locality';
        $parent_code = substr($values['code'], 0, 2) . '0000';
      }
      else {
        if (empty($parent_code)) {
          $entities = $administrative_area_storage->loadByProperties([
            'name' => $row['上级'],
          ]);
          if ($entity = reset($entities)) {
            $values['parent'] = $entity->id();
          }
          $values['type'] = 'thoroughfare';
          $parent_code = substr($values['code'], 0, 4) . '00';
        }
      }
      if ($parent_code) {
        $entities = $administrative_area_storage->loadByProperties([
          'code' => $parent_code,
        ]);
        if ($entity = reset($entities)) {
          $values['parent'] = $entity->id();
        }
      }

      $administrative_area_storage->create($values)->save();
    }

    return $this;
  }

}
