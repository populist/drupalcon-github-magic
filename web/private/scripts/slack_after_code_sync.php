<?php
// Load Slack helper functions
require_once( dirname( __FILE__ ) . '/slack_helper.php' );

if ( isset( $_POST['wf_type'] ) && $_POST['wf_type'] == 'sync_code' ) {
  // Get the committer, hash, and message for the most recent commit.
  $committer = `git log -1 --pretty=%cn`;
  $commit_message   = `git log -1 --pretty=%B`;
  $hash      = `git log -1 --pretty=%h`;

  // Setup the Text
  $message = array();
  $message['Message'] = '"' . rtrim($commit_message) . '"' . "\n";
  $message['Details'] = 'Commit _' . rtrim($hash) . '_ to the `' . $_ENV['PANTHEON_ENVIRONMENT'] . '` environment of `' . $_ENV['PANTHEON_SITE_NAME'] . '`' . ' by ' . rtrim($committer) . "\n";
  _slack_tell( $message, 'drupalcon', 'Pantheon Git', 'http://live-drupalcon-github-magic.pantheonsite.io/sites/default/files/icons/git.png', '#666666');
} 
