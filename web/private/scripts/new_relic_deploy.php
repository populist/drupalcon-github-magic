<?php

// Load Slack helper functions
require_once( dirname( __FILE__ ) . '/slack_helper.php' );

// No need to log this script operation in New Relic's stats.
if (extension_loaded('newrelic')) {
  newrelic_ignore_transaction();
}

// Fetch metadata from Pantheon's internal API.
$req = pantheon_curl('https://api.live.getpantheon.com/sites/self/bindings?type=newrelic', NULL, 8443);
$meta = json_decode($req['body'], true);

// Get the right binding for the current ENV.
$nr = FALSE;
foreach($meta as $data) {
  if ($data['environment'] === PANTHEON_ENVIRONMENT) {
    $nr = $data;
    break;
  }
}

// Fail fast if we're not going to be able to call New Relic.
if ($nr == FALSE) {
  echo "\n\nALERT! No New Relic metadata could be found.\n\n";
  exit();
}

// Get Deploy Information and Add Marker to New Relic
if (defined('PANTHEON_ENVIRONMENT') && (PANTHEON_ENVIRONMENT == 'test' || PANTHEON_ENVIRONMENT == 'live')) {
  // Topline description:
  $description = 'Deploy to environment triggered via Pantheon';
  // Find out if there's a deploy tag:
  $revision = `git describe --tags`;
  // Get the annotation:
  $changelog = `git tag -l -n99 $revision`;
  $user = $_POST['user_email'];

  // Use New Relic's v1 curl command-line example.
  $curl = 'curl -H "x-api-key:'. $data['api_key'] .'"';
  $curl .= ' -d "deployment[application_id]=' . $data['app_name'] .'"';
  $curl .= ' -d "deployment[description]= '. $description .'"';
  $curl .= ' -d "deployment[revision]='. $revision .'"';
  $curl .= ' -d "deployment[changelog]='. $changelog .'"';
  $curl .= ' -d "deployment[user]='. $user .'"';
  $curl .= ' https://api.newrelic.com/deployments.xml';
  // The below can be helpful debugging.
  // echo "\n\nCURLing... \n\n$curl\n\n";

  echo "Logging deployment in New Relic...\n";
  passthru($curl);
  echo "Done!";

  // Log this all in Slack
  $slack_channel_name = 'drupalcon';
  $slack_user_name = 'New Relic';
  $slack_user_icon = 'http://live-drupalcon-github-magic.pantheonsite.io/sites/default/files/icons/newrelic.png';
  $message = 'Adding a New Relic deploy marker to the `' . PANTHEON_ENVIRONMENT . '` environment.';
  _slack_tell($message, $slack_channel_name, $slack_user_name, $slack_user_icon, '#0ab0bf');
}
