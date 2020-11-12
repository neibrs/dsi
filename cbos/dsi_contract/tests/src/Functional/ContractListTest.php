<?php


namespace Drupal\Tests\dsi_contract\Functional;


use Drupal\Core\Url;
/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group dsi_contract
 */
class ContractListTest extends ContractTestBase {
  /**
   * A normal logged in user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $webUser;

  protected function setUp() {
    parent::setUp();
    $this->webUser = $this->drupalCreateUser([
      'view published contract entities'
    ]);
  }

  /**
   * 检测列表
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function testContractList() {

    $this->drupalLogin($this->webUser);

    // 验证
    $assertSession = $this->assertSession();
    $this->drupalGet(Url::fromRoute('entity.dsi_contract.collection'));
    $assertSession->statusCodeEquals(200);

    //创建新数据
    $contract = $this->createContract();
    // search full name 搜索验证
    $keywords = [
      'name' => $contract->label(),
    ];
    $this->drupalPostForm('entity.dsi_contract.collection', $keywords, t('Search'));
    $assertSession->statusCodeEquals(200);
    $assertSession->linkExists($contract->label());
  }

}