<?php

namespace Drupal\Tests\dsi_contract\Functional;

use Drupal\Core\Url;

/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group dsi_contract
 */
class ContractEditFormTest extends ContractTestBase {
  /**
   * A normal logged in user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $webUser;

  /**
   * 创建用户登录
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function setUp() {
    parent::setUp();
    $this->webUser = $this->drupalCreateUser([
      'edit contract entities',
    ]);
  }

  public function testContractEdit() {
    // 登录验证
    $this->drupalLogin($this->webUser);
    // 新建数据
    $contract = $this->createContract();
    // 断言会话
    $assertSession = $this->assertSession();
    // 访问验证
    $this->drupalGet(Url::fromRoute('entity.dis_contract.canonical', ['dsi_contract' => $contract->id()]));
    $assertSession->statusCodeEquals(200);
    // 点击验证
    $this->clickLink(t('Save'));
    $assertSession->assertResponse(200);
    // 修改数组
    $edit = [
      'name' => $this->randomMachineName(),
      'id' => $contract->id(),
    ];
    // 修改验证
    $this->drupalPostForm(Url::fromRoute('entity.dsi_contract.edit_form'), $edit, t('Save'));
    $assertSession->statusCodeEquals(200);

  }

}
