<?php

namespace Drupal\Tests\dsi_litigant\Functional;

use Drupal\Core\Url;

/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group dsi_litigant
 */
class LitigantListTest extends LitigantTestBase {
  /**
   * A normal logged in user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $webUser;

  protected function setUp() {
    parent::setUp();
    $this->webUser = $this->drupalCreateUser([
      'view published litigant entities',
    ]);
  }

  /**
   * 检测列表
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function testLitigantList() {

    $this->drupalLogin($this->webUser);

    // 验证
    $assertSession = $this->assertSession();
    $this->drupalGet(Url::fromRoute('entity.dsi_litigant.collection'));
    $assertSession->statusCodeEquals(200);

    // 创建新数据
    $litigant = $this->createLitigant();
    // search full name 搜索验证
    $keywords = [
      'name' => $litigant->label(),
    ];
    $this->drupalPostForm('entity.dsi_litigant.collection', $keywords, t('Search'));
    $assertSession->statusCodeEquals(200);
    $assertSession->linkExists($litigant->label());
  }

}
