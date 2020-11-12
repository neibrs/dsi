<?php


namespace Drupal\Tests\dsi_cases\Functional;


use Drupal\Core\Url;
/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group dsi_cases
 */
class CasesListTest extends CasesTestBase{
  /**
   * A normal logged in user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $webUser;

  protected function setUp() {
    parent::setUp();
    $this->webUser = $this->drupalCreateUser([
      'view published cases entities'
    ]);
  }

  /**
   * 检测列表
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function testCasesList() {

    $this->drupalLogin($this->webUser);

    // 验证
    $assertSession = $this->assertSession();
    $this->drupalGet(Url::fromRoute('entity.dsi_cases.collection'));
    $assertSession->statusCodeEquals(200);

    //创建新数据
    $cases = $this->createCases();
    // search full name 搜索验证
    $keywords = [
      'name' => $cases->label(),
    ];
    $this->drupalPostForm('entity.dsi_cases.collection', $keywords, t('Search'));
    $assertSession->statusCodeEquals(200);
    $assertSession->linkExists($cases->label());
  }

}