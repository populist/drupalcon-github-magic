<?php

// Load Slack helper functions
require_once( dirname( __FILE__ ) . '/slack_helper.php' );

// Deal with Secrets
$defaults = array();
$secrets  = _get_secrets( array( 'tinfoil_api_key', 'tinfoil_secret_key' ), $defaults );

// Define Values to Get Started
$api_key = $secrets['tinfoil_api_key'];
$secret_key = $secrets['tinfoil_secret_key'];
$test_site_name = 'drupalcon-github-magic';
$slack_channel_name = 'drupalcon';
$slack_user_name = 'SecurityTesting-with-TinFoil';
$slack_user_icon = 'http://live-drupalcon-github-magic.pantheonsite.io/sites/default/files/icons/tinfoil.png';

// If we are deploying to test, run a performace test
if (defined('PANTHEON_ENVIRONMENT') && (PANTHEON_ENVIRONMENT == 'test')) {
  $message = 'Starting a security scan on the `test` environment...' . "\n";
  _slack_tell($message, $slack_channel_name, $slack_user_name, $slack_user_icon);
  $message = array();
  $message['Test Conditions'] = 'Scanning for _57 known vulnerabilities_ with the *TinFoil Security* platform';
  $curl = curl_init();
  $curl_options = array(
    CURLOPT_URL => 'https://www.tinfoilsecurity.com/api/v1/sites/' . $test_site_name . '/scans',
    CURLOPT_HTTPHEADER => array('Authorization:Token token=' . $secret_key . ', access_key=' . $api_key),
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_CUSTOMREQUEST => "POST",
  );
  curl_setopt_array($curl, $curl_options);
  $curl_response = json_decode(curl_exec($curl));
  curl_close($curl);

  if (isset($curl_response->scans->id)) {
    $message['Test Results'] = 'https://www.tinfoilsecurity.com/sites/' . $test_site_name . '/scans/' . $curl_response->scan->id . '/report/overview' . "\n";
  }
  else {
    $message = array();
    $message['Error'] = $curl_response->errors[0] . "\n";
  }
  _slack_tell($message, $slack_channel_name, $slack_user_name, $slack_user_icon, '#ffb347');
}
