<?php

namespace Drupal\Tests\acquia_vwo\Functional;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Tests\BrowserTestBase;

/**
 * VWO Test Base.
 *
 * @group vwo
 */
class VwoTestBase extends BrowserTestBase {

  use StringTranslationTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'vwo',
  ];

  /**
   * A user without the "Administer Acquia VWO" permission.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $authUser;

  /**
   * A user with the "Administer Acquia VWO" permission.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->defaultTheme = 'minimal';
  }

  /**
   * Creates an authenticated user.
   */
  public function createAuthUser() {
    $this->authUser = $this->drupalCreateUser([
      'access content',
    ]);
  }

  /**
   * Creates an admin user.
   */
  public function createAdminUser() {
    $this->adminUser = $this->drupalCreateUser([
      'access content',
      'administer site configuration',
      'administer vwo',
    ]);
  }

  /**
   * Asserts whether an expected cache context was present in the last response.
   *
   * @param string $expected_cache_context
   *   The expected cache context.
   */
  protected function assertCacheContext($expected_cache_context) {
    $cache_contexts = $this->getHeaderContents('X-Drupal-Cache-Contexts');
    $this->assertContains($expected_cache_context, $cache_contexts, "'" . $expected_cache_context . "' is present in the X-Drupal-Cache-Contexts header.");
  }

  /**
   * Returns page headers.
   *
   * @param string $name
   *   The header name.
   *
   * @return string[]
   *   The page headers.
   */
  protected function getHeaderContents($name) {
    return explode(' ', $this->getSession()->getResponseHeader($name));
  }

  /**
   * Asserts that a cache context was not present in the last response.
   *
   * @param string $not_expected_cache_context
   *   The expected cache context.
   */
  protected function assertNoCacheContext($not_expected_cache_context) {
    $cache_contexts = $this->getHeaderContents('X-Drupal-Cache-Contexts');
    $this->assertNotContains($not_expected_cache_context, $cache_contexts, "'" . $not_expected_cache_context . "' is not present in the X-Drupal-Cache-Contexts header.");
  }

  /**
   * Asserts whether an expected cache tag was present in the last response.
   *
   * @param string $expected_cache_tag
   *   The expected cache tag.
   */
  protected function assertCacheTag($expected_cache_tag) {
    $cache_tags = $this->getHeaderContents('X-Drupal-Cache-Tags');
    $this->assertContains($expected_cache_tag, $cache_tags, "'" . $expected_cache_tag . "' is present in the X-Drupal-Cache-Tags header.");
  }

  /**
   * Asserts that a cache tag was not present in the last response.
   *
   * @param string $not_expected_cache_tag
   *   The expected cache tag.
   */
  protected function assertNoCacheTag($not_expected_cache_tag) {
    $cache_tags = $this->getHeaderContents('X-Drupal-Cache-Tags');
    $this->assertNotContains($not_expected_cache_tag, $cache_tags, "'" . $not_expected_cache_tag . "' is not present in the X-Drupal-Cache-Tags header.");
  }

  /**
   * Asserts that a header contains a value.
   *
   * @param string $name
   *   The header name.
   * @param string $value
   *   The expected value.
   */
  protected function assertHeader($name, $value) {
    $header_value = $this->getHeaderContents($name);
    $this->assertContains($value, $header_value, "$value is present in the $name header.");
  }

}
