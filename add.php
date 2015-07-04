<?php
session_start();
//$server = !empty($_SESSION['server']) ? $_SESSION['server'] : 'production';
//if (isset($_POST['change'])) {
  //$server = ($server == 'production') ? 'sandbox' : 'production';
  //$_SESSION['server'] = $server;
//}

$_SESSION['server'] = $server = 'sandbox';

require_once 'credentials.php';
require_once 'envoy.class.inc';
require_once 'krumo/class.krumo.php';

if (!empty($_POST)) {
  $data = new stdClass();

  $attributes = new stdClass();
  $attributes->title = $_POST['title'];
  $attributes->contentencoded = $_POST['body'];
  $data->attributes = $attributes;

  $profileLink = new stdClass();
  $profileLink->href = $creds[$server]['host'] . '/profiles/story';
  $data->links->profile[0] = $profileLink;

  // insert data! 
  $call = new PMPCall($creds[$server]['host'], $creds[$server]['client_id'], $creds[$server]['client_secret']);
  $doc = $call->push($data);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>

  <!-- Basic Page Needs
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta charset="utf-8">
  <title>ENVOY :: ADD STORY</title>
  <meta name="description" content="">
  <meta name="author" content="">



  <!-- Favicon
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link rel="icon" type="image/png" href="/favicon2.ico" />

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

<div class="row" id="dash">
  <div class="two columns">
  <form action="add.php" method="post">
  <input class="button-primary" type="submit" name='change' value="<?php print $server; ?>" />
  </form>
</div>
</div>

<?php if (isset($call->results[0])) print '<div class="row">' . krumo($call->results[0]) . '</div>'; ?>

<form action="add.php" method="post">
<div class="row">

<input class="u-full-width" name="title" type="text" placeholder="Title" value="">

  <!--<label for="body">Body</label>-->
  <textarea class="u-full-width" placeholder="Write a story" name="body"></textarea>

  <input class="button-primary" type="submit" value="Submit">
</form>

</div>
  </body>
</html>
