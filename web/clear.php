<?php
header("Content-type:application/json");
$image_url= "https://" . $_ENV["PANTHEON_ENVIRONMENT"]. "-". $_ENV["PANTHEON_SITE_NAME"].".pantheonsite.io/themes/museum/assets/images/drupalcon-baltimore_720.png" ;

$response = array("response_type" => "in_channel",
                  "mrkdwn" => "true",
                  "text" =>"Let's get this party started...",
                  "attachments"=> [
                    array("image_url"=>$image_url,"color"=> "#ff0000"),array("image_url"=>$image_url,"color"=> "#00ff00"),array("image_url"=>$image_url,"color"=> "#0000ff"),array("image_url"=>$image_url,"color"=>"#9400D3")
                  ]
                );
echo json_encode($response)
?>
