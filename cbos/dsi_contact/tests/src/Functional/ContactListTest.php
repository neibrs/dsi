<?php

namespace Drupal\Tests\dsi_contact\Functional;

use Drupal\Core\Url;

/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group dsi_contact
 */
class ContactListTest extends ContactTestBase {
  /**
   * A normal logged in user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $webUser;

  protected function setUp() {
    parent::setUp();
    $this->webUser = $this->drupalCreateUser([
      'view published contact entities',
    ]);
  }

  /**
   * 检测列表
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function testContactList() {

    $this->drupalLogin($this->webUser);

    // 验证
    $assertSession = $this->assertSession();
    $this->drupalGet(Url::fromRoute('entity.dsi_contact.collection'));
    $assertSession->statusCodeEquals(200);

    // 创建新数据
    $contact = $this->createContact();
    // search full name 搜索验证
    $keywords = [
      'name' => $contact->label(),
    ];
    $this->drupalPostForm('entity.dsi_contact.collection', $keywords, t('Search'));
    $assertSession->statusCodeEquals(200);
    $assertSession->linkExists($contact->label());
  }

}
