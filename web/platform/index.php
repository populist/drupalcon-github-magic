<?php

/**
 * This file only returns JSON for the API calls. The actual app + HTML is in index.html.
 */

setcookie('NO_CACHE', '1');

if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')){
  header("Location: index.html#/diagram/live");
  die();
}

/**
 * Showcase of Pantheon's abilities.
 */
header('Content-type: application/json');
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$curl_result = pantheon_curl('https://api.live.getpantheon.com/sites/self/bindings', NULL, 8443);
$attr_result = pantheon_curl('https://api.live.getpantheon.com/sites/self', NULL, 8443);
//print $attr_result['body'];
//var_dump($curl_result);
$json_array = json_decode($curl_result['body'], true);
//print json_encode($json_array, JSON_PRETTY_PRINT);
//die;

$output = new stdClass();
$output->servers = array();

// Hard code edge servers.
$edge = new stdClass();
$edge->id = 'AAAAAAAA';
$edge->type = 'edgeserver';
$edge->endpoint = 'edge1';
$edge->slave_of = NULL;
$output->servers[] = $edge;

if (!isset($_GET['env'])){
  $_GET['env'] = PANTHEON_ENVIRONMENT;
}

// Make a smaller JSON structure to return to the frontend.
foreach ($json_array as $key => $val) {


  if ($val['environment'] == $_GET['env'] && !($val['type'] == 'pingdom')) {
    if(!isset($val['failover'])){
      $type = $val['type'];
      if(isset($val['slave_of']) && $type == "dbserver"){
        $type = "slavedbserver";
      }
      $binding = new stdClass();
      $binding->id = (string)substr($key, 0, 8);
      $binding->endpoint = isset($val['endpoint']) ? $val['endpoint'] : NULL;
      $binding->type = $type;
      $binding->slave_of = isset($val['slave_of']) ? $val['slave_of'] : NULL;
      $output->servers[] = $binding;
    }
  }
}

print json_encode($output);
