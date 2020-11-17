<?php

namespace Drupal\Tests\dsi_record\Functional;

use Drupal\Core\Url;

/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group dsi_record
 */
class RecordListTest extends RecordTestBase {
  /**
   * A normal logged in user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $webUser;

  protected function setUp() {
    parent::setUp();
    $this->webUser = $this->drupalCreateUser([
      'view published record entities',
    ]);
  }

  /**
   * 检测列表
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function testRecordList() {

    $this->drupalLogin($this->webUser);

    // 验证
    $assertSession = $this->assertSession();
    $this->drupalGet(Url::fromRoute('entity.dsi_record.collection'));
    $assertSession->statusCodeEquals(200);

    // 创建新数据
    $record = $this->createRecord();
    // search full name 搜索验证
    $keywords = [
      'name' => $record->label(),
    ];
    $this->drupalPostForm('entity.dsi_record.collection', $keywords, t('Search'));
    $assertSession->statusCodeEquals(200);
    $assertSession->linkExists($record->label());
  }

}
