<?php

/**
 * @file
 * Administration forms and support functions of the Drupal VWO module.
 *
 * Offload the large amount of admin only code to its own file so it does not
 * get loaded and parsed with every single page load.
 */

/**
 * Root Settings form.
 */
function vwo_settings($form, &$form_state) {

  $settings = array_merge(
    _vwo_settings_defaults(),
    variable_get('vwo_settings', array())
  );

  $form['id'] = array(
    '#type' => 'textfield',
    '#title' => t('VWO Account ID'),
    '#description' => implode('', array(
      t('Your numeric account ID or "NONE". This is the number after <q>var _vis_opt_account_id =</q> in the VWO Smart Code.'),
      '<br /><strong>',
      t('You can use the <a href="@url">Extract Account ID</a> tool to extract the Account ID from the full VWO Smart Code.', array(
        '@url' => url('admin/config/system/vwo/vwoid'),
      )),
      '</strong>',
    )),
    '#size' => 15,
    '#maxlength' => 20,
    '#required' => TRUE,
    '#default_value' => $settings['id'],
  );

  $form['synchtype'] = array(
    '#type' => 'radios',
    '#title' => t('Javascript loading method'),
    '#description' => '<p>' . implode('</p><p>', array(
      t('Asynchronous loading is now the default. Please see <a target="_blank" href="https://vwo.com/blog/asynchronous-code/">https://vwo.com/blog/asynchronous-code/</a> for more details.'),
      t('The Asynchronous version of VWO code reduces page load time as the VWO code is downloaded in parallel to site code. It also ensures that your site is never slowed down even if the VWO servers are inaccessible.'),
      t('VWO have extensively tested the asynchronous code across variety of browsers (including IE7) and it works perfectly.'),
    )) . '</p>',
    '#options' => array(
      'async' => t('Asynchronous (default)'),
      'sync' => t('Synchronous'),
    ),
    '#default_value' => $settings['synchtype'],
  );

  $form['advanced'] = array(
    '#type' => 'fieldset',
    '#title' => t('Advanced Asynchronous Settings'),
    '#description' => implode('', array(
      '<p>',
      t('These settings are only used when Asynchronous loading mode is selected above.'),
      '</p><p>',
      t('Note that in normal circumstances, all the data and files that need to be download will get downloaded in 100-200 milliseconds, so the following timeouts are an absolute maximum threshold and can safely be kept as is.'),
      '</p><p>',
      t('One possible side effect of decreasing these timeouts, would be users on slower internet connections reaching the timeout and as a result, missing out on becoming part of the test.'),
      '</p>',
    )),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#states' => array(
      'visible' => array(
        ':input[name=synchtype]' => array('value' => 'async'),
      ),
    ),

    'asynctollibrary' => array(
      '#type' => 'textfield',
      '#title' => t('Test Library Download Timeout'),
      '#description' => t('The maximum time in milliseconds the code snippet will wait for the VWO javascript library to be downloaded from the Amazon Cloudfront Content Delivery Network. If the library is not available in this time, your original page will be displayed without tests. Default: 1500 ms.'),
      '#size' => 10,
      '#default_value' => $settings['asynctollibrary'],
    ),

    'asynctolsettings' => array(
      '#type' => 'textfield',
      '#title' => t('Test Settings Download Timeout'),
      '#description' => t('The maximum time in milliseconds the code snippet will wait for test settings to arrive from the VWO servers. If no settings arrive within this period, your original page will be displayed without tests. Default: 2000 ms.'),
      '#size' => 10,
      '#default_value' => $settings['asynctolsettings'],
    ),

    'asyncusejquery' => array(
      '#type' => 'radios',
      '#title' => t('Use existing jQuery'),
      '#description' => t('Configure the "use_existing_jquery" option in the code snippet. Please provide feedback to the module Author regarding your experiences with this setting.'),
      '#options' => array(
        'local' => t('True'),
        'import' => t('False (default)'),
      ),
      '#default_value' => $settings['asyncusejquery'],
    ),
  );

  $form['actions'] = array(
    '#type' => 'actions',

    'submit' => array(
      '#type' => 'submit',
      '#value' => t('Save configuration'),
    ),
  );

  return $form;
}

/**
 * Validation for settings form.
 *
 * Verify that the account ID, and two threshold values are numeric.
 */
function vwo_settings_validate($form, &$form_state) {
  if (!preg_match('/^\d+$/', $form_state['values']['id']) && $form_state['values']['id'] != 'NONE') {
    form_set_error('id', t('Your VWO ID must be numeric. If you have having issues locating it, please use the <a href="@parse_url">Parse Account Id tool</a>.', array('@parse_url' => url('admin/config/system/vwo/vwoid'))));
  }

  if (!preg_match('/^\d+$/', $form_state['values']['asynctolsettings'])) {
    form_set_error('asynctolsettings', t('Asynchronous Test Settings Download Timeout must be numeric. It is a value in milliseconds.'));
  }

  if (!preg_match('/^\d+$/', $form_state['values']['asynctollibrary'])) {
    form_set_error('asynctollibrary', t('Asynchronous Test Library Download Timeout must be numeric. It is a value in milliseconds.'));
  }
}

/**
 * Handler for vwo_setup_form.
 *
 * Submit handler for vwo_setup_form form.
 * Push all settings into one variable since they are always used together.
 */
function vwo_settings_submit($form, &$form_state) {
  // Start with defaults.
  $settings = array_merge(
    _vwo_settings_defaults(),
    variable_get('vwo_settings', array())
  );

  foreach ($settings as $key => $value) {
    if (isset($form_state['values'][$key])) {
      $settings[$key] = $form_state['values'][$key];
    }
  }

  variable_set('vwo_settings', $settings);

  drupal_set_message(t('VWO settings have been saved.'), 'status');
}

/**
 * Form to accept full cut'n'paste of Smart Code.
 *
 * Form to accept full cut'n'paste of Smart Code from website and pull
 * Account ID from it using preg_match().
 */
function vwo_parse_account_id() {
  $form = array();

  $settings = array_merge(
    _vwo_settings_defaults(),
    variable_get('vwo_settings', array())
  );

  // Display current ID if it exists.
  $form['current_id'] = array(
    '#type' => 'textfield',
    '#title' => t('Current Account ID'),
    '#disabled' => TRUE,
    '#default_value' => $settings['id'],
    '#size' => 15,
  );

  // Text area to paste into.
  $form['parse_area'] = array(
    '#type' => 'textarea',
    '#title' => t('Paste full VWO Smart Code here'),
    '#description' => t('The Smart Code is available by logging into the VWO website and from your dashboard, selecting the <em><strong>Settings</strong></em> menu down the left side, and then <em><strong>Smart Code</strong></em> along the top. Select all the text in the <em><strong>VWO Smart Code</strong></em> text box and copy it here.'),
    '#rows' => '15',
  );

  $form['actions'] = array(
    '#type' => 'actions',

    // Submit button.
    'extract' => array(
      '#type' => 'submit',
      '#value' => t('Extract Account ID from pasted VWO Smart Code'),
    ),
  );

  return $form;
}

/**
 * Validation handler for vwo_parse_account_id().
 *
 * Ensures that the the Account ID can be parsed from the pasted code and then
 * stores it for the sumbit handler. The pasted code can either be the sync or
 * async code.
 */
function vwo_parse_account_id_validate($form, &$form_state) {

  // Synchronous version of code.
  if (preg_match('/var _vis_opt_account_id = ([[:digit:]]+);/', $form_state['values']['parse_area'], $matches)) {
    $form_state['values']['parsed_id'] = $matches[1];
  }

  // Asynchronous version of code.
  elseif (preg_match('/var account_id=([[:digit:]]+),/', $form_state['values']['parse_area'], $matches)) {
    $form_state['values']['parsed_id'] = $matches[1];
  }

  // Failure.
  else {
    form_set_error('parse_area', t('Unable to locate Account ID in pasted code.'));
  }
}

/**
 * Submit handler for vwo_parse_account_id().
 *
 * Saves the Account ID stored from validation handler.
 */
function vwo_parse_account_id_submit($form, &$form_state) {

  // Pull existing settings, adjust the Account ID, save them.
  $settings = array_merge(
    _vwo_settings_defaults(),
    variable_get('vwo_settings', array())
  );
  $settings['id'] = $form_state['values']['parsed_id'];
  variable_set('vwo_settings', $settings);

  drupal_set_message(t('Saved VWO Account ID parsed from pasted code: @id', array('@id' => $settings['id'])), 'status');

  $form_state['redirect'] = 'admin/config/system/vwo';
}

/**
 * Settings to configure the filters.
 *
 * That determine when this javascript should be added to
 * the page in hook_init().
 */
function vwo_filters() {
  $form = array();

  $settings = array_merge(
    _vwo_settings_defaults(),
    variable_get('vwo_settings', array())
  );

  $form['enabled_fieldset'] = array(
    '#type' => 'fieldset',
    '#title' => t('Enable Filter Processing'),
    '#collapsible' => TRUE,
    '#collapsed' => ($settings['enabled'] == 'on'),

    'enabled' => array(
      '#type' => 'radios',
      '#title' => t('Enable VWO'),
      '#description' => '<p>' . implode('</p><p>', array(
        t('This option can be used to globally turn off this modules processing of filters to include the Smart Code on pages.'),
        t('The VWO Smart Code may still be manually included on any page by calling <em>vwo_add_js()</em> from your own module. Please see example directory for a template module of one way of doing that.'),
      )) . '</p>',
      '#options' => array(
        'off' => t('Disabled'),
        'on' => t('Enabled'),
      ),
      '#default_value' => $settings['enabled'],
    ),
  );

  // User configuration options.
  $form['userfilter'] = array(
    '#type' => 'fieldset',
    '#title' => t('User specific visibility settings'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,

    'userradios' => array(
      '#type' => 'radios',
      '#title' => t('Custom visibility settings'),
      '#description' => t('Allow individual users to optin/out to being included in the tests in their account settings.'),
      '#options' => array(
        'nocontrol' => t('Users cannot control whether or not the javascript is added.'),
        'optout' => t('Include javascript by default, but let individual users turn it off.'),
        'optin' => t('Do not include javascript by default but let individual users turn it on.'),
      ),
      '#default_value' => $settings['userfilter'],
    ),

    '#states' => array(
      'visible' => array(
        ':input[name=enabled]' => array('value' => 'on'),
      ),
    ),
  );

  // Role options.
  $role_options = user_roles();
  $form['rolefitler'] = array(
    '#type' => 'fieldset',
    '#title' => t('Role specific visibility settings'),
    '#collapsible' => TRUE,

    'role_negation' => array(
      '#type' => 'radios',
      '#title' => t('Add VWO for specific roles'),
      '#options' => array(
        'normal' => t('Add to the selected roles only'),
        'negated' => t('Add to every role except the selected ones'),
      ),
      '#default_value' => $settings['rolefilter_neg'],
    ),

    'rolechecks' => array(
      '#type' => 'checkboxes',
      '#title' => t('Roles'),
      '#description' => t('Include VWO javascript only for the selected role(s). If you select no roles, it will be included for all users. If a user has any of the roles checked, that user be included (or excluded, depending on the negation setting above).'),
      '#options' => $role_options,
      '#default_value' => $settings['rolefilter'],
    ),

    '#states' => array(
      'visible' => array(
        ':input[name=enabled]' => array('value' => 'on'),
      ),
    ),
  );

  // Page specific options.
  $access = module_exists('php') && user_access('use PHP for settings');
  $page_options = array(
    'listexclude' => t('Show on every page except the listed pages.'),
    'listinclude' => t('Show on only the listed pages.'),
    'usephp' => t('Show if the following PHP code returns <code>TRUE</code> (PHP-mode, experts only).'),
  );

  $form['pagefilter'] = array(
    '#type' => 'fieldset',
    '#title' => t('Page specific visibility settings'),
    '#collapsible' => TRUE,

    '#states' => array(
      'visible' => array(
        ':input[name=enabled]' => array('value' => 'on'),
      ),
    ),
  );

  $form['pagefilter']['pageradios'] = array(
    '#type' => 'radios',
    '#title' => t('Include VWO javascript on specific pages'),
    '#options' => $page_options,
    '#default_value' => $settings['pagefilter'],
  );

  $form['pagefilter']['pagelist'] = array(
    '#type' => 'textarea',
    '#title' => t('Pages'),
    '#description' => t("Enter one page per line as Drupal paths. The '*' character is a wildcard. Example paths are %blog for the blog page and %blog-wildcard for every personal blog. %front is the front page.", array(
      '%blog' => 'blog',
      '%blog-wildcard' => 'blog/*',
      '%front' => '<front>',
    )),
    '#default_value' => $settings['pagelist'],
  );

  // If filter is set to usephp, but this user has no access to edit this,
  // then we pass the configuration as values so they can't be edited.
  if ($settings['pagefilter'] == 'usephp' && !$access) {

    // Replace displayed version with disabled versions and pass values.
    $form['pagefilter']['pageradios_off'] = $form['pagefilter']['pageradios'];
    $form['pagefilter']['pageradios_off']['#disabled'] = TRUE;
    $form['pagefilter']['pageradios'] = array(
      '#type' => 'value',
      '#value' => 'usephp',
    );
    $form['pagefilter']['pagelist_off'] = $form['pagefilter']['pagelist'];
    $form['pagefilter']['pagelist_off']['#disabled'] = TRUE;
    $form['pagefilter']['pagelist'] = array(
      '#type' => 'value',
      '#value' => $settings['pagelist'],
    );
  }

  elseif (!$access) {
    unset($form['pagefilter']['pageradios']['#options']['usephp']);
  }

  if ($access) {
    $form['pagefilter']['pagelist']['#description'] .= ' ' . t('If the PHP-mode is chosen, enter PHP code between %php. Note that executing incorrect PHP-code can break your Drupal site.', array('%php' => '<' . '?php ?' . '>'));
  }

  $form['actions'] = array(
    '#type' => 'actions',

    'submit' => array(
      '#type' => 'submit',
      '#value' => t('Save settings'),
    ),
  );

  return $form;
}

/**
 * Submit handler for vwo_filters().
 *
 * Start from defaults and add new settings.
 */
function vwo_filters_submit($form, &$form_state) {
  $settings = array_merge(
    _vwo_settings_defaults(),
    variable_get('vwo_settings', array())
  );

  $settings['enabled'] = $form_state['values']['enabled'];

  $settings['userfilter'] = $form_state['values']['userradios'];

  // Only store the roles that are checked.
  $settings['rolefilter'] = array_filter($form_state['values']['rolechecks']);
  $settings['rolefilter_neg'] = $form_state['values']['role_negation'];

  // Page listing.
  $settings['pagefilter'] = $form_state['values']['pageradios'];
  $settings['pagelist'] = trim($form_state['values']['pagelist']);

  variable_set('vwo_settings', $settings);

  drupal_set_message(t('VWO settings have been saved.'), 'status');
}
