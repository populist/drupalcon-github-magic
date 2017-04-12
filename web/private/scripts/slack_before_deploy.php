<?php
// Load Slack helper functions
require_once( dirname( __FILE__ ) . '/slack_helper.php' );

if ( isset( $_POST['wf_type'] ) && $_POST['wf_type'] == 'deploy' ) {
	$deploy_tag     = `git describe --tags`;
	$deploy_message = $_POST['deploy_message'];
	if (empty($deploy_message)){
		$text = "(no message)";
	} else {
		$text = $deploy_message;
	}
  $output = array();
  $output['Deploy Message'] = $text;
  $output['Environment'] = '`' . $_ENV['PANTHEON_ENVIRONMENT'] . '`';
  $output['Deployed By'] = $_POST['user_email'];
  $output['Site Name'] = '`' . $_ENV['PANTHEON_SITE_NAME'] . '`';
	_slack_tell( $output, 'drupalcon', 'Pantheon Deployment', 'http://live-drupalcon-github-magic.pantheonsite.io/sites/default/files/icons/pantheon.png', '#EFD01B');
}
