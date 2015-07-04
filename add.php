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
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- FONT
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <!--<link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>-->

  <!-- CSS
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <!--<link rel="stylesheet" href="css/normalize.css">-->
  <!--<link rel="stylesheet" href="css/skeleton.css">-->
  <!--<link rel="stylesheet" href="css/style.css">-->
  
  <link rel="stylesheet" href="css/pure-min.css">
  
  <!--[if lte IE 8]>
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-old-ie-min.css">
<![endif]-->
<!--[if gt IE 8]><!-->
    <link rel="stylesheet" href="css/grids-responsive-min.css">
<!--<![endif]-->  
  
  <style>
    .pure-g > div {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }
    .l-box {
        padding: 1em;
    }
    
    /*
When setting the primary font stack, apply it to the Pure grid units along
with `html`, `button`, `input`, `select`, and `textarea`. Pure Grids use
specific font stacks to ensure the greatest OS/browser compatibility.
*/
html, button, input, select, textarea,
.pure-g [class *= "pure-u"] {
    /* Set your content font stack here: */
    font-family: Arial, Helvetica, sans-serif;
}
  </style>
  
</head>

<body>
<div id="container" class="pure-g">
<div class="pure-u-1-1 l-box">

<div class="pure-g">
  <div class="pure-u-1-1 1-box">
  <form action="index.php" method="post">
  <input class="pure-button" type="submit" name='change' value="<?php print $server; ?>" />
  </form>
</div>
</div>

<?php if (isset($call->results[0])) print '<div class="row">' . krumo($call->results[0]) . '</div>'; ?>

<form action="add.php" method="post" class="pure-form pure-form-stacked">

<fieldset>
        <legend>Add a Story</legend>

  <input name="title" type="text" placeholder="Title" value="" class="pure-input-1">

  <textarea placeholder="Write a story" name="body" class="pure-input-1"></textarea>

    <button type="submit" class="pure-button pure-button-primary">Submit</button>
<fieldset>
</form>

</div>
</div>
  </body>
</html>
