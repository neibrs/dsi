<?php

namespace Drupal\Tests\eabax_core\Functional;

use Drupal\Core\Url;

trait UpdateTestTrait {

  public function updateTest() {
    /** @var \Drupal\Tests\WebAssert $assert_session */
    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('system.status'));
    $assert_session->responseNotContains('Mismatched entity and/or field definitions');
  }

}
