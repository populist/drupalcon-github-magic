<?php

// Load Slack helper functions
require_once( dirname( __FILE__ ) . '/slack_helper.php' );

// Assemble the Arguments
$slack_type = $argv[1]; // Argument One
$slack_message = $argv[2]; // Argument Two
$slack_message_type = $argv[3]; // Argument Three

switch($slack_type) {
  case 'behat': 
    $slack_agent = 'Behat';
    $slack_icon = 'http://live-drupalcon-nola-demo.pantheonsite.io/sites/default/files/icons/behat.png';
    $slack_color = '#00ff00';
    $slack_message = 'Kicking off Behavioral Testing with Behat...';
    _slack_tell( $slack_message, 'drupalcon', $slack_agent, $slack_icon, $slack_color);
    $slack_message = array();
    $slack_message['Tests'] = 'TODO, TODO2';
    _slack_tell( $slack_message, 'drupalcon', $slack_agent, $slack_icon, $slack_color);
    break;
  case 'behat_finished':
    $slack_agent = 'Behat';
    $slack_icon = 'http://live-drupalcon-nola-demo.pantheonsite.io/sites/default/files/icons/behat.png';
    $slack_color = '#00ff00';
    $slack_message = 'Testing complete. Build: *PASSED*';
    _slack_tell( $slack_message, 'drupalcon', $slack_agent, $slack_icon, $slack_color);
    break;
  case 'composer':
    $slack_agent = 'Composer';
    $slack_icon = 'http://live-drupalcon-nola-demo.pantheonsite.io/sites/default/files/icons/composer.png';
    $slack_color = '#000080';
    $slack_message = 'Running Composer to create build artifact...';
    _slack_tell( $slack_message, 'drupalcon', $slack_agent, $slack_icon, $slack_color);
    $slack_message = array();
    $slack_message['Command'] = 'composer -n build-assets';
    $slack_message['Results'] = '116 installs, 0 updates, 0 removals';
    _slack_tell( $slack_message, 'drupalcon', $slack_agent, $slack_icon, $slack_color);
    break;
  case 'circle':
    $slack_agent = 'CircleCI';
    $slack_icon = 'http://live-drupalcon-nola-demo.pantheonsite.io/sites/default/files/icons/circle.png';
    $slack_color = '#229922';
    $slack_message = 'New code is detected! Time to kick off a new build...';
    _slack_tell( $slack_message, 'drupalcon', $slack_agent, $slack_icon, $slack_color);
    $slack_message = array();
    $slack_message['Source Branch'] = 'master';
    $slack_message['Source Repository'] = 'https://github.com/populist/drupalcon-github-magic';
    $slack_message['Build ID'] = $argv[2];
    $slack_message['Build URL'] = 'https://circleci.com/gh/populist/drupalcon-github-magic/' . $argv[2];
    _slack_tell( $slack_message, 'drupalcon', $slack_agent, $slack_icon, $slack_color);
    break;
  case 'grunt':
    $slack_agent = 'Grunt JS';
    $slack_icon = 'http://live-drupalcon-nola-demo.pantheonsite.io/sites/default/files/icons/grunt.png';
    $slack_color = '#e48632';
    $slack_message = 'Running Grunt Javascript Task Runner...';
    _slack_tell( $slack_message, 'drupalcon', $slack_agent, $slack_icon, $slack_color);
    $slack_message = array();
    $slack_message['Target Directory'] = 'web/themes/basic';
    $slack_message['Grunt Operations'] = 'SASS Compilation';
    $slack_message['Files'] = '_colors.css_, _fonts.css_, _layout.css_';
    $slack_message['Status'] = 'Complete';
    _slack_tell( $slack_message, 'drupalcon', $slack_agent, $slack_icon, $slack_color);
    break;
  case 'terminus':
    $slack_agent = 'Terminus';
    $slack_icon = 'http://live-drupalcon-nola-demo.pantheonsite.io/sites/default/files/icons/terminus.png';
    $slack_color = '#1ec503';
    $slack_message = "Authenticating to Pantheon with Terminus machine token...";
    _slack_tell( $slack_message, 'drupalcon', $slack_agent, $slack_icon, $slack_color);
    $slack_message = array();
    $slack_message['CLI Version'] = '1.1.2';
    $slack_message['CLI User'] = 'matt@pantheon.me';
    _slack_tell( $slack_message, 'drupalcon', $slack_agent, $slack_icon, $slack_color);
    break;
  case 'pantheon_multidev':
    $slack_agent = 'Terminus';
    $slack_icon = 'http://live-drupalcon-nola-demo.pantheonsite.io/sites/default/files/icons/terminus.png';
    $slack_color = '#1ec503';
    $slack_message = "Setting up Pantheon Multidev testing environment...";
    _slack_tell( $slack_message, 'drupalcon', $slack_agent, $slack_icon, $slack_color);
    $slack_message = array();
    $slack_message['Environment'] = 'ci-test';
    $slack_message['Site'] = 'drupalcon-github-magic';
    $slack_message['Operation'] = 'terminus build-env:push-code';
    $slack_message['Environment URL'] = 'https://ci-test-drupalcon-github-magic.pantheonsite.io';
    _slack_tell( $slack_message, 'drupalcon', $slack_agent, $slack_icon, $slack_color);
    break;
  case 'pantheon_multidev_finished':
    $slack_agent = 'Terminus';
    $slack_icon = 'http://live-drupalcon-nola-demo.pantheonsite.io/sites/default/files/icons/terminus.png';
    $slack_color = '#1ec503';
    $slack_message = "Build artifact successfully pushed to Multidev environment!";
    _slack_tell( $slack_message, 'drupalcon', $slack_agent, $slack_icon, $slack_color);
    break;
  case 'pantheon_dev':
    $slack_agent = 'Terminus';
    $slack_icon = 'http://live-drupalcon-nola-demo.pantheonsite.io/sites/default/files/icons/terminus.png';
    $slack_color = '#1ec503';
    $slack_message = "Setting up Pantheon Dev environment...";
    _slack_tell( $slack_message, 'drupalcon', $slack_agent, $slack_icon, $slack_color);
		$slack_message = array();
		$slack_message['Environment'] = 'dev';
    $slack_message['Site'] = 'drupalcon-github-magic';
    $slack_message['Operation'] = 'terminus build-env:merge';
    $slack_message['Environment URL'] = 'https://ci-test-drupalcon-github-magic.pantheonsite.io';
    _slack_tell( $slack_message, 'drupalcon', $slack_agent, $slack_icon, $slack_color);
    break;
  case 'pantheon_dev_finished':
    $slack_agent = 'Terminus';
    $slack_icon = 'http://live-drupalcon-nola-demo.pantheonsite.io/sites/default/files/icons/terminus.png';
    $slack_color = '#1ec503';
    $slack_message = "Build artifact successfully pushed to Dev environment!";
    _slack_tell( $slack_message, 'drupalcon', $slack_agent, $slack_icon, $slack_color);
    break;
}
