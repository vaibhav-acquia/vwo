<?php

/**
 * @file
 * Help page fragments for VWO module.
 */

/**
 * Help page for both the generic help for the module, and settings page.
 */
function _vwo_help_settings() {

  list($vwo_url_nopartner) = _vwo_help_links();
  $vwo_settings = url('admin/config/system/vwo');

  $h[] = t('VWO is an easy to use A/B, split and multivariate testing tool.');
  $h[] = t('In order to use this module, you will need a VWO account. A Free 30 day trial account is available from the VWO website by clicking on the link below.');
  $h2[] = t(
    '<a href="@url">Sign up for a Free 30 day trial without affiliate link.</a>',
    array('@url' => $vwo_url_nopartner)
  );
  $h[] = implode('', $h2);
  $h[] = t(
    'Once the Account ID has been set on <a href="@url">settings page</a>, the configuration of your A/B and other tests is done by logging into your account on the VWO website.',
    array('@url' => $vwo_settings)
  );

  return '<p>' . implode('</p><p>', $h) . '</p>';
}

/**
 * Help page for the filter page.
 */
function _vwo_help_filter() {

  $h[] = t('VWO adds javascript to each page by default. If you are only running tests on parts of your site, or only wish to include certain users in your tests based on Drupal settings, you may filter which pages the javascritpt is added to with these filters.');
  $h[] = t('If more than one setting on this page is set, they are treated as <em>BOOLEAN AND</em>.');
  $h[] = t('The JS code can be added to pages at any stage of the render by calling <em>vwo_include_js()</em> from your own custom module. Please see the examples directory for a template of one way to doing this.');

  return '<p>' . implode('</p><p>', $h) . '</p>';
}

/**
 * Help page for the Extract VWO ID page.
 */
function _vwo_help_vwoid() {

  list($vwo_url_nopartner) = _vwo_help_links();

  $h[] = t('Your VWO Account ID can be extracted from the "VWO Smart Code" available from the VWO site.');
  $hl[] = t('Log into your VWO account.');
  $hl[] = t('Locate the <em><strong>Settings</strong></em> menu down the left side of the site.');
  $hl[] = t('Click on <em><strong>Smart Code</strong></em> located along the top tabs.');
  $hl[] = t('Copy and paste the <em><strong>VWO Smart Code</strong></em> contents into the text box below.');
  $h[] = t('This can be found by following these simple directions:')
    . '<ul><li>' . implode('</li><li>', $hl) . '</li></ul>';

  $h[] = t('In order to use this module, you will need a VWO account. A Free 30 day trial account is available from the VWO website by clicking the link below.');
  $h2[] = t(
    '<a href="@url">Sign up for a Free 30 day trial without affiliate link.</a>',
    array('@url' => $vwo_url_nopartner)
  );
  $h[] = implode('', $h2);

  return '<p>' . implode('</p><p>', $h) . '</p>';
}

/**
 * Helper function to return the partner and noparter links to VWO.
 */
function _vwo_help_links() {

  $vwo_url_nopartner = url(
    'https://vwo.com/free-trial/',
    array(
      'absolute' => TRUE,
      'query' => array(),
    )
  );

  return array($vwo_url_nopartner);
}
