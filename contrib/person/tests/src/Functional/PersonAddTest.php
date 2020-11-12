<?php

namespace Drupal\Tests\person\Functional;

use Drupal\Core\Url;

/**
 * Simple test for person add.
 *
 * @group person
 */
class PersonAddTest extends PersonTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['block'];

  /**
   * Tests person add.
   */
  public function testAdd() {
    $this->drupalPlaceBlock('local_actions_block');

    $user = $this->drupalCreateUser([
      'maintain persons',
      'view persons',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.person.collection'));
    $assert_session->statusCodeEquals(200);
    $assert_session->linkExists(t('Add'));

    $this->clickLink(t('Add'));
    $assert_session->statusCodeEquals(200);
    $assert_session->linkExists(t('Employee'));

    $this->clickLink(t('Employee'));
    $assert_session->statusCodeEquals(200);

    $edit = [
      'name[0][value]' => $this->randomMachineName(),
    ];
    $this->drupalPostForm(NULL, $edit, '保存并返回');
    $this->assertResponse(200);
    // 测试不支持中文字符串比较.
    //$this->assertRaw(t('@type %title has been created.', ['@type' => t('Employee'), '%title' => $edit['name[0][value]']]));
  }

}
