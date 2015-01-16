<?php
session_start();
$server = !empty($_SESSION['server']) ? $_SESSION['server'] : 'production';
?>
<!DOCTYPE html>
<html lang="en">
<head>

  <!-- Basic Page Needs
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta charset="utf-8">
  <title>DEPUTY</title>
  <meta name="description" content="">
  <meta name="author" content="">

  <!-- Mobile Specific Metas
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

  <!-- FONT
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <!--<link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>-->
  <!-- CSS
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link rel="stylesheet" href="css/normalize.css">
  <link rel="stylesheet" href="css/skeleton.css">
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
<div class="container">
<div><a class="button button-primary" href="/">HOME</a></div>
<?php

require_once 'credentials.php';

require_once 'deputy.class.inc';

require_once 'krumo/class.krumo.php';

if (!empty($_GET['guid'])) {
  $call = new PMPCall($creds[$server]['host'], $creds[$server]['client_id'], $creds[$server]['client_secret']);
  $options = array('guid' => $_GET['guid']);
  $call->pull($options);
  $doc = $call->query->results->docs[0];
  $title = htmlspecialchars($doc->attributes->title, ENT_QUOTES, 'UTF-8');
  $profile = htmlspecialchars($doc->profile, ENT_QUOTES, 'UTF-8');

  print "<h2>$title [$profile]</h2>";
  print "<h3>GUID: {$doc->guid}</h3>";
  if ($doc->hasEnclosure()) {
    if ($doc->profile == 'image') {
      print "<image width=\"300\" src=\"{$doc->getHREF()}\">";
    }
    else if ($doc->profile == 'audio') {
      print "<audio controls src=\"{$doc->getHREF()}\"></audio>";
    }
    else {
      print "<{$doc->profile} src=\"{$doc->getHREF()}\"></{$doc->profile}>";
    }
  }
  print "<h4>CollectionDocJson Object</h4>";
  krumo($call->query->results->fullDoc);
  print "<h4>PMPDoc Object</h4>";
  krumo($doc);
  print "<h4>PMPCall Object</h4>";
  krumo($call);
}
?>
</div>
</body>
</html>