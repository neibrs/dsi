<?php

namespace Drupal\Tests\person\Functional;

use Drupal\Core\StreamWrapper\PublicStream;
use Drupal\Core\Url;
use Drupal\Tests\field_ui\Traits\FieldUiTestTrait;

/**
 * Simple test for person import.
 *
 * @group person
 */
class PersonImportTest extends PersonTestBase {

  use FieldUiTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['block', 'field_ui'];

  /**
   * Tests import.
   */
  public function testImport() {
    $this->drupalPlaceBlock('local_actions_block');

    $user = $this->drupalCreateUser([
      'maintain persons',
      'view persons',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.person.collection'));
    $assert_session->linkExists(t('Import'));

    $this->clickLink(t('Import'));
    $this->assertResponse(200);

    $source = '/var/www/html/modules/dsi/contrib/person/tests/data/person.xls';
    $file = \Drupal::service('file_system')->copy($source, PublicStream::basePath());
    $edit = [
      'migration' => 'person_xls',
      'file' => $file,
    ];
    $this->drupalPostForm(NULL, $edit, t('Import'));
    $assert_session->statusCodeEquals(200);
    // Tests imported data
  }

  /**
   * 测试导入字段映射界面：自定义字段能映射.
   */
  public function testFieldImport() {
    // 字段配置链接是在 lock_tasks 区块中.
    $this->drupalPlaceBlock('local_tasks_block');

    $user = $this->drupalCreateUser([
      'maintain persons',
      'view persons',
      'administer person fields',
    ]);
    $this->drupalLogin($user);

    // 为人员添加自定义字段
    $field_name = mb_strtolower($this->randomMachineName());
    $field_label = $this->randomMachineName();
    $this->fieldUIAddNewField('person/type/employee/edit', $field_name, $field_label, 'text');

    // 测试导入字段映射界面
    $this->drupalGet('entity.person.collection');
    $this->clickLink(t('Import'));

    $source = '/var/www/html/modules/dsi/contrib/person/tests/data/person.xls';
    $file = \Drupal::service('file_system')->copy($source, PublicStream::basePath());
    $edit = [
      'migration' => 'person_xls',
      'file' => $file,
    ];
    $this->drupalPostForm(NULL, $edit, t('Import'));
    $this->assertFieldByXPath("//select[@name='sources[${field_label}][column]']", NULL, '自定义字段映射');
  }

}
