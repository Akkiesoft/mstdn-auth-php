<?php
require_once("common.php");

$scheme = (isset($_SESSION['scheme'])) ? $_SESSION['scheme'] : "";
$domain = (isset($_SESSION['domain'])) ? $_SESSION['domain'] : "";
if ($domain == "") {
  print "Error: Not set domain";
  exit();
}
$app = json_decode(file_get_contents("instances/".$domain));

if (isset($_GET['code'])) {
  $data = array(
    "grant_type"    => "authorization_code",
    "redirect_uri"  => $callback,
    "client_id"     => $app->client_id,
    "client_secret" => $app->client_secret,
    "code"          => $_GET['code']
  );
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $scheme."://".$domain."/oauth/token");
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  $result = json_decode(curl_exec($ch));
  curl_close($ch);
  $token = $result->access_token;
} else {
  print "Error: Failed to get access token";
  exit();
}

$header = [
  'Authorization: Bearer '.$token,
  'Content-Type: application/json',
];
$data = array(
  // Toot
  'status' => "NYAAAAAAN"
);
$ch = curl_init();
#curl_setopt($ch, CURLOPT_URL, $scheme."://".$domain."/api/v1/accounts/verify_credentials");
curl_setopt($ch, CURLOPT_URL, $scheme."://".$domain."/api/v1/statuses");
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$result = curl_exec($ch);
curl_close($ch);

unset($_SESSION);
session_destroy();
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>Mastodon authentication sample for PHP</title>
  </head>
  <body>
    <p>Result:</p>
    <textarea>{$result}</textarea>
  </body>
</html>
