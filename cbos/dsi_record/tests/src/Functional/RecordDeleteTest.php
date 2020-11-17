<?php

namespace Drupal\Tests\dsi_record\Functional;

use Drupal\Core\Url;

/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group dsi_record
 */
class RecordDeleteTest extends RecordTestBase {

  /**
   * A normal logged in user.
   * @var \Drupal\user\UserInterface
   */
  protected $webUser;

  /**
 * 赋予登录用户删除权限
 */
  protected function setUp() {
    parent::setUp();
    $this->webUser = $this->drupalCreateUser([
      'delete record entities',
    ]);
  }

  public function testRecordDelete() {
    // 新建数据
    $record = $this->createRecord();
    // 验证登录
    $this->drupalLogin($this->webUser);
    // 断言会话
    $assertSession = $this->assertSession();
    // 访问验证
    $this->drupalGet(Url::fromRoute('entity.dsi_record.delete_form', ['dsi_record' => $record->id()]));
    $assertSession->statusCodeEquals(200);
    // 点击验证
    $this->clickLink(t('Delete'));
    $assertSession->statusCodeEquals(200);

  }

}
