<?php

// Load Slack helper functions
require_once( dirname( __FILE__ ) . '/slack_helper.php' );

// Automagically import config into your D8 site upon code deployment
_slack_tell('Importation of Drupal 8 Configuration for the `' . PANTHEON_ENVIRONMENT . '` environment is starting...', 'drupalcon', 'Drush-on-Pantheon', 'http://live-drupalcon-github-magic.pantheonsite.io/sites/default/files/icons/drush.png', '#A9A9A9');
$output = array();
exec('drush config-import -y', $output);
if (count($output) > 0) {
  $output = preg_replace('/\s+/', ' ', array_slice($output, 1, -1));
  $output = str_replace(' update', ' [update]', $output);
  $output = str_replace(' create', ' [create]', $output);
  $output = str_replace(' delete', ' [delete]', $output);
  $output = implode($output, "\n");
  $output = rtrim($output);
  $output = array($output);
} else {
  $output = array('No new configuration to import.');
}
exec('drush cache-rebuild');
_slack_tell($output, 'drupalcon', 'Drush-on-Pantheon', 'http://live-drupalcon-github-magic.pantheonsite.io/sites/default/files/icons/drush.png', '#A9A9A9');
