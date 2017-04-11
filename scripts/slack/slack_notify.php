<?php

// Load Slack helper functions
require_once( dirname( __FILE__ ) . '/slack_helper.php' );

// Assemble the Arguments
$slack_type = $argv[1]; // Argument One
$slack_message = $argv[2]; // Argument Two

switch($slack_type) {
  case 'behat': 
    $slack_agent = 'Behat';
    $slack_icon = 'http://live-drupalcon-nola-demo.pantheonsite.io/sites/default/files/icons/behat.png';
    $slack_color = '#00ff00';
    break;
  case 'composer':
    $slack_agent = 'Composer';
    $slack_icon = 'http://live-drupalcon-nola-demo.pantheonsite.io/sites/default/files/icons/composer.png';
    $slack_color = '#0000ff';
    break;
  case 'circle':
    $slack_agent = 'CircleCI';
    $slack_icon = 'http://live-drupalcon-nola-demo.pantheonsite.io/sites/default/files/icons/circle.png';
    $slack_color = '#EE2E24';
    break;
}

_slack_tell( $slack_message, 'drupalcon', $slack_agent, $slack_icon, $slack_color);
