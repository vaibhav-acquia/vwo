<?php

namespace Drupal\Tests\vwo\Functional;

use Drupal\user\Entity\User;

// cspell:ignore userconfig nocontrol nodetypes
// cspell:ignore listexclude listinclude optout
// cspell:ignore Pathconfig Userconfig Visualisation
/**
 * VWO Integration Tests.
 *
 * @group vwo
 */
class VwoIntegrationTest extends VwoTestBase {

  /**
   * {@inheritdoc}
   */
  protected $profile = 'standard';

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'vwo',
  ];

  /**
   * VWO settings.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  private $config;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->createAuthUser();
    $this->config = \Drupal::configFactory()->getEditable('vwo.settings');
  }

  /**
   * Test integration on multi-language site.
   */
  public function testMultiLanguage() {
    // Configure VWO module and VWO module.
  }

  /**
   * Test Cache tags and cache contexts.
   *
   * Make sure the values are only added to the pages selected in
   * the Visualisation plugin.
   */
  public function testCacheTagsContexts() {
    $this->drupalGet('user');
    $this->assertSession()->statusCodeEquals(200);
    // Check cache contexts.
    $this->assertCacheContext('user.roles:authenticated');
    // Check cache tags.
    $this->assertNoCacheTag('config:vwo.settings');

    // Perform login.
    $this->drupalLogin($this->authUser);
    // Check cache contexts.
    $this->assertCacheContext('user');
    // Check cache tags.
    $this->assertNoCacheTag('config:vwo.settings');
  }

  /**
   * Test visibility settings.
   */
  public function testVisibility() {
    $this->setInitialConfig();

    // Create content.
    $this->createContent();

    // Test Scripts.
    $this->checkScripts();

    // Check cache.
    $this->checkCaches();

    // Test scripts on user basis configuration.
    $this->checkUserOptInOut();

    // Test scripts on node type basis.
    $this->checkNodeType();

    // Test role level visibility.
    $this->checkRoles();

    // Test path based visibility.
    $this->checkPathConfiguration();
  }

  /**
   * Helper to initialize configuration.
   */
  public function setInitialConfig() {
    $this->config->set('id', '123456')
      ->set('filter.enabled', 'on')
      ->set('filter.nodetypes', ['article'])
      ->set('filter.userconfig', 'nocontrol')
      ->save();
  }

  /**
   * Helper to create content.
   */
  public function createContent() {
    $storage = \Drupal::entityTypeManager()->getStorage('node');

    // Create node.
    $node = $storage->create(
      [
        'type' => 'article',
        'title' => $this->t('Test article'),
        'path' => '/vwo_test',
        'status' => TRUE,
      ]
    );
    $node->save();
  }

  /**
   * Test scripts on the page.
   */
  public function checkScripts() {
    // Check scripts.
    $this->drupalGet('node/1');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->responseContains('window._vwo_code || (function');
    $this->assertSession()->responseContains('var account_id=123456');
    $this->assertSession()->responseContains('settings_tolerance=2000');
    $this->assertSession()->responseContains('v=d.querySelector(\'#vwoCode\')');
  }

  /**
   * Tests Drupal caches.
   */
  public function checkCaches() {
    $this->assertHeader('X-Drupal-Cache', 'MISS');
    $this->drupalGet('node/1');
    $this->assertHeader('X-Drupal-Cache', 'HIT');
  }

  /**
   * Helper to test visibility based on user profile configuration.
   */
  public function checkUserOptInOut() {
    // Log in.
    $this->drupalLogin($this->authUser);

    // Change visibility to never include the script
    // unless the user decides to include.
    $this->setUserconfig('optout');

    // Update user config to opt out.
    $this->updateUserData(0);

    // Check that script is not added to the page.
    $this->drupalGet('node/1');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->responseNotContains('window._vwo_code');
    // Check cache contexts.
    $this->assertCacheContext('user');
    // Check cache tags.
    $this->assertNoCacheTag('config:vwo.settings');

    // Update user config to opt in.
    $this->updateUserData(1);

    // Check that script is added to the page.
    $this->drupalGet('node/1');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->responseContains('window._vwo_code');
    // Check cache contexts.
    $this->assertCacheContext('user');
    // Check cache tags.
    $this->assertCacheTag('config:vwo.settings');

    // Change visibility to always include the script
    // unless the user decides not to include.
    $this->setUserconfig('optin');

    // Update user config to opt out.
    $this->updateUserData(0);

    // Check that script is not added to the page.
    $this->drupalGet('node/1');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->responseNotContains('window._vwo_code');

    // Update user config to opt in.
    $this->updateUserData(1);

    // Check that script is added to the page.
    $this->drupalGet('node/1');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->responseContains('window._vwo_code');

    // Reset config.
    $this->setUserconfig('nocontrol');
  }

  /**
   * Helper to change userconfig.
   *
   * @param string $opt
   *   Opt in or out.
   */
  public function setUserconfig($opt) {
    $this->config->set('filter.userconfig', $opt)->save();
  }

  /**
   * Helper to update the user data to opt in/out.
   *
   * @param string $opt
   *   Opt in or out.
   */
  public function updateUserData($opt) {
    // Load the current user.
    $current_user = \Drupal::currentUser();
    $user = User::load($current_user->id());
    // Update field userconfig.
    \Drupal::service('user.data')->set('vwo', $user->id(), 'userconfig', $opt);
  }

  /**
   * Helper to test content type visibility.
   */
  public function checkNodeType() {
    // Disable integration on all content types.
    $this->setContentTypeVisibility([]);

    $this->drupalGet('node/1');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()
      ->responseNotContains('window._vwo_code || (function');

    // Enable integration on Vwo Article.
    $this->setContentTypeVisibility(['article']);
    $this->drupalGet('node/1');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->responseContains('window._vwo_code || (function');
    // Check cache contexts.
    $this->assertCacheContext('url.path');
  }

  /**
   * Helper to change visibility on content type basis.
   *
   * @param array $node_types
   *   List of node types to enable integration.
   */
  public function setContentTypeVisibility($node_types) {
    $this->config->set('filter.nodetypes', $node_types)->save();
  }

  /**
   * Helper to test role level visibility.
   */
  public function checkRoles() {
    // Set visibility only for anonymous users.
    $this->setRoleVisibility(['anonymous']);

    // Check script as anonymous.
    $this->drupalLogout();
    $this->drupalGet('node/1');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->responseContains('window._vwo_code || (function');
    // Check cache contexts.
    $this->assertCacheContext('user.roles');

    // Check as authenticated user.
    $this->drupalLogin($this->authUser);
    $this->drupalGet('node/1');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()
      ->responseNotContains('window._vwo_code || (function');

    // Remove role basis configuration.
    $this->setRoleVisibility([]);
    $this->drupalGet('node/1');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->responseContains('window._vwo_code || (function');
    // Check cache contexts.
    $this->assertCacheContext('user');
  }

  /**
   * Helper to change visibility on role basis.
   *
   * @param array $roles
   *   List of roles to enable integration.
   */
  public function setRoleVisibility($roles) {
    $this->config->set('filter.roles', $roles)->save();
  }

  /**
   * Helper to test visibility based on paths.
   */
  public function checkPathConfiguration() {
    // Include on all pages, but the path /vwo_test.
    $this->setPathconfig('/vwo_test', 'listexclude');
    $this->drupalGet('node/1');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()
      ->responseNotContains('window._vwo_code || (function');

    // Include on path /vwo_test only.
    $this->setPathconfig('/vwo_test', 'listinclude');
    $this->drupalGet('node/1');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->responseContains('window._vwo_code || (function');

    // Reset configuration.
    $this->setPathconfig('', 'listexclude');

    // Include on path /vwo_test only.
    $this->setPathconfig('/vwo_test', 'listinclude');
    $this->drupalGet('node/1');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()
      ->responseContains('window.VWO.data = window.VWO.data || {};');

    // Reset configuration.
    $this->setPathconfig('', 'listexclude');
  }

  /**
   * Helper to change paths config.
   *
   * @param string $paths
   *   The paths.
   * @param string $type
   *   Can be listexclude or listinclude.
   */
  public function setPathconfig($paths, $type) {
    $this->config->set('filter.page.type', $type)->save();
    $this->config->set('filter.page.list', $paths)->save();
  }

}
