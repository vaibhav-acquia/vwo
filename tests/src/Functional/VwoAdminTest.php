<?php

namespace Drupal\Tests\acquia_vwo\Functional;

/**
 * Tests VWO admin settings.
 *
 * @group vwo
 */
class VwoAdminTest extends VwoTestBase {

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->createAuthUser();

    $this->adminUser = $this->drupalCreateUser([
      'access content',
      'administer site configuration',
      'administer modules',
      'administer permissions',
      'administer vwo',
    ]);
  }

  /**
   * Test access to the admin pages.
   */
  public function testAdminAccess() {
    // Anonymous user.
    $this->drupalGet('/admin/config/system/vwo');
    $this->assertSession()->pageTextContains($this->t('Access denied'));

    // Normal user.
    $this->drupalLogin($this->authUser);
    $this->drupalGet('/admin/config/system/vwo');
    $this->assertSession()->pageTextContains($this->t('Access denied'));

    // Admin user.
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('/admin/config/system/vwo');
    $this->assertSession()->pageTextNotContains($this->t('Access denied'));
    $this->assertSession()->pageTextContains($this->t('VWO Setup'));
  }

  /**
   * Test links on module page.
   */
  public function testModulePage() {
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('/admin/modules');

    // Test link to permissions.
    $this->assertSession()
      ->elementExists('css', 'a[href*="/admin/people/permissions/module/vwo"][id="edit-modules-vwo-links-permissions"]');

    // Test link to configure.
    $this->assertSession()
      ->elementExists('css', 'a[href*="/admin/config/system/vwo"][id="edit-modules-vwo-links-configure"]');
  }

}
