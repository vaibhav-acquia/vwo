# Synopsis

VWO lets you run A/B, split URL, and multivariate tests with ease and deliver
optimum user experiences to your website visitors. Once you insert the VWO 
SmartCode snippet into the head section of your website front-end code, you’re 
all set to start testing new ideas. The VWO plugin for Drupal automates and 
simplifies the configuration and setup of VWO snippets.

# Requirements

To integrate VWO with Drupal, you need to have an active VWO account. 
You can get started with VWO by simply signing up for a 30 day 
free trial account.

Here are the implementation steps for VWO <> Drupal:

## A. Install from .zip file -

1. Download VWO plugin from here
2. Login to Drupal’s admin panel and navigate to the Modules section.
3. Click Install new module.
4. On the next page, upload the VWO plugin downloaded earlier and click Install.
5. Once the plugin is installed, you will see the successful installation 
   message. On this page, click Enable newly added modules.
6. Select the checkbox next to the VWO module and then click Save configuration.

## B. Install using Composer (only in Drupal 8.9 and above) -

1. Run this command on your server ->
`composer require 'drupal/visual_website_optimizer:^1.1'`
2. Once the plugin is installed, you will see the successful installation 
   message. On this page, click Enable newly added modules.
3. Select the checkbox next to the VWO module and then click Save configuration.



# Configuration steps -

Here are the steps to save your configuration with VWO module:

1. After you install the module, a message displays asking you to configure 
   your VWO plug-in.
2. Enable VWO (as shown in the above picture).
3. Enter VWO Account ID.
4. Save configuration.

The VWO module will now be installed and enabled.

For a detailed walkthrough, here’s how to integrate VWO with Drupal.


# About VWO

VWO is an experimentation platform that enables brands to improve their key 
business metrics by empowering teams to run their conversion optimization 
programs easily. Whether your goal is to boost website conversion or improve 
user experience, VWO is the go-to tool for any experimentation needs. We provide 
capabilities to unify customer data, discover customer behavioral insights, 
build hypotheses, run A/B tests on server, web, and mobile, rollout features, 
personalize experiences, and improve engagement across the entire customer 
journey.

VWO’s global customer base includes brands like Ubisoft, Nvidia, Allstate, etc. 
It has helped more than 4,500 brands across the globe to run over 600,000 
experiments to date.

# Local features

By integrating VWO with Drupal, you get the following benefits:

Support for Synchronous and Asynchronous VWO javascript loading.
Freedom to choose which pages should include the VWO javascript.
Account ID parser to locate your Account ID from the supplied javascript.
