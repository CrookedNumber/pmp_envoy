<?php
session_start();
$server = !empty($_SESSION['server']) ? $_SESSION['server'] : 'production';
if (isset($_POST['change'])) {
  $server = ($server == 'production') ? 'sandbox' : 'production';
  $_SESSION['server'] = $server;
}

if (!empty($_GET['guid']) && preg_match("/[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab]{1}[0-9a-f]{3}-[0-9a-f]{12}/", $_GET['guid'])) {
  header("Location: view.php?guid=" . $_GET['guid']);
  die();
}

$profiles = array(
  'story',
  'image',
  'audio',
  'video',
  'organization',
  'group',
  'user',
);

$creators = array(
  "APM" => "98bf597a-2a6f-446c-9b7e-d8ae60122f0d",
  "NPR" => "6140faf0-fb45-4a95-859a-070037fafa01",
  "NPRDS" => "39b744ba-e132-4ef3-9099-885aef0ff2f1",
  "PBS" => "fc53c568-e939-4d9c-86ea-c2a2c70f1a99",
  "PRI" => "7a865268-c9de-4b27-a3c1-983adad90921",
  "PRX" => "609a539c-9177-4aa7-acde-c10b77a6a525",
);

$params = array(
  'limit',
  'profile',
  'creator',
  'text',
  'tag',
  'guid',
);

$new_params = array();
$redirect = FALSE;
foreach ($params as $param) {
  if (isset($_GET[$param]) && strlen((trim($_GET[$param]))) > 0) {
    $new_params[$param] = $_GET[$param];
  }
  elseif (isset($_GET[$param])) {
    $redirect = TRUE;
  }
}

if ($redirect && !empty($_GET)) {
  header("Location: index.php?" . http_build_query($new_params));
  die();  
}

?>
<!DOCTYPE html>
<html lang="en">
<head>

  <!-- Basic Page Needs
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta charset="utf-8">
  <title>ENVOY</title>
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
<?php

require_once 'credentials.php';
require_once 'envoy.class.inc';
require_once 'krumo/class.krumo.php';

$page = (!empty($_GET['page'])) ? (int) $_GET['page'] : 1;
$profile = (!empty($_GET['profile']) && in_array($_GET['profile'], $profiles)) ? $_GET['profile'] : '';
$creator = (!empty($_GET['creator']) && in_array($_GET['creator'], array_keys($creators))) ? $_GET['creator'] : '';
$tag = (!empty($_GET['tag'])) ? htmlspecialchars($_GET['tag'], ENT_QUOTES, 'UTF-8') : '';
$text = (!empty($_GET['text'])) ? htmlspecialchars($_GET['text'], ENT_QUOTES, 'UTF-8') : '';

$options = array();
$options['limit'] = 10;
$options['profile'] = (!empty($_GET['profile']) && in_array($_GET['profile'], $profiles)) ? $_GET['profile'] : NULL;
$options['creator'] = (!empty($_GET['creator']) && in_array($_GET['creator'], array_keys($creators))) ? $creators[$_GET['creator']] : NULL;
$options['text'] = (!empty($_GET['text'])) ? $_GET['text'] : NULL;
$options['tag'] = (!empty($_GET['tag'])) ? $_GET['tag'] : NULL;

?>

<div class="row" id="dash">
  <div class="two columns">
  <form action="index.php" method="post">
  <input class="button-primary" type="submit" name='change' value="<?php print $server; ?>" />
  </form>
</div>


<div class="ten columns">

<form action="index.php" method="get">
    <select name="profile">
      <option value=''>Profile:</option>
      <?php foreach($profiles as $v) {
        $selected = ($v == $profile) ? 'selected' : '';
        print "<option $selected value='$v'>$v</option>";
      } ?>
    </select>
    
    <select name="creator">
      <option value="">Creator:</option>
      <?php foreach($creators as $k => $v) {
        $selected = ($k == $creator) ? 'selected' : '';
        print "<option $selected value='$k'>$k</option>";
      } ?>
    </select>

    <input name="tag" type="text" placeholder="Tag(s)" value="<?php print $tag; ?>">
    <input name="text" type="text" placeholder="Search term(s)" value="<?php print $text; ?>">
    <input name="guid" type="text" placeholder="Enter a PMP GUID" value="">
    <input class="button-primary" type="submit" value="GO" />
</form>
</div>
</div>
<?php
$call = new PMPCall($creds[$server]['host'], $creds[$server]['client_id'], $creds[$server]['client_secret']);
$call->pull($options);
print "<div class='row'>" . krumo($call->query->results->fullDoc) . "</div>";
?>
<div class="row">
<table><thead><tr><th>TITLE</th><th>GUID</th><!--<th>VIEW</th><th>EDIT</th><th>DELETE</th>--><th>PROFILE</th><th>CREATOR</th><th>PUBLISHED</th></tr></thead><tbody>
<?php
if (!empty($call->query->results->docs)) {
  foreach($call->query->results->docs as $doc) {
    $date = date('D, M jS Y \a\t g:i a', strtotime($doc->attributes->published));
    $creator_href = $doc->links->creator[0]->href;
    $doc_creator = array_pop(explode('/', $creator_href));
    $creator_name = (in_array($doc_creator, $creators)) ? array_search($doc_creator, $creators) : '';
    print "<tr><td><a href='view.php?server={$server}&guid={$doc->guid}'>{$doc->attributes->title}</a></td><td>$doc->guid</td>";
    //print "<td><a href='view.php?server={$server}&guid={$doc->guid}'>view</a></td>";
    //print "<td><a href='edit.php?guid={$doc->guid}'>edit</a></td><td><a href='delete.php?guid={$doc->guid}'>delete</a></td>";  
    print "<td>{$doc->profile}</td>";
    print "<td>$creator_name</td>";
    print "<td>$date</td>";
    print "</tr>";
  }
}
else {
  print "<tr><td colspan='4'>Sorry, no results for this query.</td></tr>";
}
?>
</tbody>
</table>
<?php if ($pages > 1): ?>
  <div id ="pager">
  
  </div>
<?php endif; ?>
</div>
</div>
  </body>
</html>
