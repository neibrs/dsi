<?php

namespace Drupal\Tests\entity_type_resource\Functional;

use Drupal\Core\Url;
use Drupal\Tests\rest\Functional\ResourceTestBase;
use Psr\Http\Message\ResponseInterface;

/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group entity_type_resource
 */
class LoadTest extends ResourceTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['entity_type_resource'];

  protected static $format = 'json';

  /**
   * {@inheritdoc}
   */
  protected function setUpAuthorization($method) {
    $this->grantPermissionsToTestedRole(['access content']);
  }

  /**
   * {@inheritdoc}
   */
  protected function assertResponseWhenMissingAuthentication($method, ResponseInterface $response) {
    // TODO: Implement assertResponseWhenMissingAuthentication() method.
  }

  /**
   * {@inheritdoc}
   */
  protected function assertNormalizationEdgeCases($method, Url $url, array $request_options) {
    // TODO: Implement assertNormalizationEdgeCases() method.
  }

  /**
   * {@inheritdoc}
   */
  protected function assertAuthenticationEdgeCases($method, Url $url, array $request_options) {
    // TODO: Implement assertAuthenticationEdgeCases() method.
  }

  /**
   * {@inheritdoc}
   */
  protected function getExpectedUnauthorizedAccessCacheability() {
    // TODO: Implement getExpectedUnauthorizedAccessCacheability() method.
  }

  /**
   * Tests GET a entity_type.
   */
  public function testGet() {
    $url = Url::fromUri('base:entity_type/user')->setOption('query', ['_format' => static::$format]);

    $response = $this->request('GET', $url, $this->getAuthenticationRequestOptions('GET'));
    // TODO assert
  }

}
