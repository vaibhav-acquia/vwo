<?php

namespace Drupal\vwo\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

class VwoService {
    protected $configFactory;
    protected $currentUser;
    protected $pathMatcher;
    protected $currentPath;
    protected $entityTypeManager;
    protected $routeMatch;

    public function __construct(ConfigFactoryInterface $configFactory, AccountProxyInterface $currentUser, PathMatcherInterface $pathMatcher, CurrentPathStack $currentPath, EntityTypeManagerInterface $entityTypeManager, RouteMatchInterface $routeMatch) {
        $this->configFactory = $configFactory;
        $this->currentUser = $currentUser;
        $this->pathMatcher = $pathMatcher;
        $this->currentPath = $currentPath;
        $this->entityTypeManager = $entityTypeManager;
        $this->routeMatch = $routeMatch;
    }

    public function addPageAttachments(array &$attachments) {
        // Your existing logic from vwo_page_attachments function
    }

    public function alterUserForm(&$form, FormStateInterface $form_state, $form_id) {
        // Your existing logic from vwo_form_user_form_alter function
    }

    public function submitUserForm($form, FormStateInterface $form_state) {
        // Your existing logic from _vwo_form_user_form_alter_submit function
    }

    public function provideHelp($route_name, RouteMatchInterface $route_match) {
        // Your existing logic from vwo_help function
    }
}
