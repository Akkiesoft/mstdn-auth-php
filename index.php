<?php
require_once("common.php");

if ($_POST['login']) {
  $_SESSION['scheme'] = $_POST['scheme'];
  $_SESSION['domain'] = $_POST['domain'];
  $app    = create_app($_SESSION['scheme'], $_SESSION['domain']);

  $data = array(
    "response_type"    => "code",
    "redirect_uri"     => $callback,
    "scope"            => $scope,
    "client_id"        => $client_id
  );
  header("Location: ".$scheme."://".$domain."/oauth/authorize?".http_build_query($data));
}

function create_app($scheme, $domain) {
  global $scope, $callback;
  # check exists known instance
  if (file_exists("instances/".$domain) === FALSE) {
    # unknown instance, create app
    $data = array(
      'client_name'   => "Mastodon authentication sample for PHP",
      'redirect_uris' => $callback,
      'scopes'        => $scope
    );
    $fp = fopen("instances/".$domain, "w");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $scheme."://".$domain."/api/v1/apps");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
  }
  return json_decode(file_get_contents("instances/".$domain));
}
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>Mastodon authentication sample for PHP</title>
  </head>
  <body>
    <h2>Login</h2>
    <form action="index.php" method="post">
      <input name="scheme" value="https">://<input name="domain"><br>
      <input name="login" type="submit" value="Login">
    </form>
  </body>
</html>
