<?php

// Load Slack helper functions
require_once( dirname( __FILE__ ) . '/slack_helper.php' );

// Deal with Secrets
$defaults = array();
$secrets  = _get_secrets( array( 'loader_api_key' ), $defaults );

// Define Values to Get Started
$api_key = $secrets['loader_api_key'];
$test_id = 'a747d3fa27c22fffeee4739c8a9e3709';
$slack_channel_name = 'drupalcon';
$slack_user_name = 'PerformanceTesting-with-LoaderIO';
$slack_user_icon = 'http://live-drupalcon-nola-demo.pantheonsite.io/sites/default/files/icons/loaderio.png';

// If we are deploying to test, run a performace test
if (defined('PANTHEON_ENVIRONMENT') && (PANTHEON_ENVIRONMENT == 'test')) {
  $message = 'Starting a performance test on the test environment...' . "\n";
  _slack_tell($message, $slack_channel_name, $slack_user_name, $slack_user_icon);
  $message = array();
  $message['Test Conditions'] = 'Testing _50 virtual users_ over _60 seconds_ with the *Loader.io* platform.';
  $curl = curl_init();
  $curl_options = array(
    CURLOPT_URL => 'https://api.loader.io/v2/tests/' . $test_id . '/run',
    CURLOPT_HTTPHEADER => array('loaderio-auth: ' . $api_key),
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_CUSTOMREQUEST => "PUT",
  );
  curl_setopt_array($curl, $curl_options);
  $curl_response = json_decode(curl_exec($curl));
  curl_close($curl);

  if ($curl_response->message == 'success') {
    $message['Test Results'] = 'https://loader.io/reports/' . $test_id . '/results/' . $curl_response->result_id . "\n";
  }
  else {
    $message = array();
    $message['Error'] = print_r($curl_response,true). "\n";
  }
  _slack_tell($message, $slack_channel_name, $slack_user_name, $slack_user_icon, '#add8e6');
}
