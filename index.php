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
  'property',
);

$creators = array(
  "APM" => "98bf597a-2a6f-446c-9b7e-d8ae60122f0d",
  "NPR" => "6140faf0-fb45-4a95-859a-070037fafa01",
  "NPRDS" => "39b744ba-e132-4ef3-9099-885aef0ff2f1",
  "PBS" => "fc53c568-e939-4d9c-86ea-c2a2c70f1a99",
  "PRI" => "7a865268-c9de-4b27-a3c1-983adad90921",
  "PRX" => "609a539c-9177-4aa7-acde-c10b77a6a525",
);

$properties = array(
  "America Abroad" => '1c7ab9f3-fd4b-4039-87ba-05462c02ef35',
  "To the Best of Our Knowledge" => 'eb1f5e12-7d8a-42ba-a110-5e8e6df8463f',
  "The Takeaway" => '87c50cc3-656e-487b-ae4d-3af1fe08acc0',
  "Studio 360" => '4f1a8e65-5dde-49f4-8c45-1905c4b7dc4f',
  "Living on Earth" => 'f1f14a6b-8318-4577-b678-c00504600038',
  "Science Friday"	=> 'cd697523-2943-4cb5-9abd-1ceb4c448820',
  "American Homefront Project" => '4c6e24e5-484f-49e8-be8d-452cfddd6252',
  "American RadioWorks" => '75c5c431-653e-4552-a230-b9c5b90ea78d',
  "Next Avenue" => '8e12401e-2ed0-4f82-8e86-dec72255be37',
  "PRI’s The World" => '4d3a942d-91c0-46a5-86df-9338f88c8487',
  "Reveal" => 'b6039f52-337b-4d68-b971-c5b1079c0770',
  "American Routes" => 'ee84fee0-19f8-4b7f-8a0b-5b69e84d1f7e',
  "Sound Opinions" => '344e430e-de37-4261-bf6a-6280972f3cc7',
  "The Moth" => '9a5e5095-c9a5-44cc-9788-4093d6390c7e',
  "The Dinner Party Download" => 'e5c41ac5-ff37-41a7-9ddc-a103c473b043',
  "The Splendid Table" => '714b1185-63b6-41b7-8c79-07d6e9700c4b',
  "Marketplace" => '3e3b6243-31c6-4686-bb88-a8e8446f0c2a',
  "MPR News" => '8d9de351-0b81-47bf-92a9-d962bae5d6de',
);
ksort($properties);

$params = array(
  'limit',
  'profile',
  'creator',
  'property',
  'text',
  'tag',
  'guid',
  'page',
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
<?php

require_once 'credentials.php';
require_once 'envoy.class.inc';
require_once 'krumo/class.krumo.php';

$page = (!empty($_GET['page'])) ? (int) $_GET['page'] : 1;
$profile = (!empty($_GET['profile']) && in_array($_GET['profile'], $profiles)) ? $_GET['profile'] : '';
$creator = (!empty($_GET['creator']) && in_array($_GET['creator'], array_keys($creators))) ? $_GET['creator'] : '';
$property = (!empty($_GET['property']) && in_array($_GET['property'], array_keys($properties))) ? $_GET['property'] : '';

$tag = (!empty($_GET['tag'])) ? htmlspecialchars($_GET['tag'], ENT_QUOTES, 'UTF-8') : '';
$text = (!empty($_GET['text'])) ? htmlspecialchars($_GET['text'], ENT_QUOTES, 'UTF-8') : '';

$options = array();
$options['limit'] = 10;
$options['offset'] = 10 * ($page-1);
$options['profile'] = (!empty($_GET['profile']) && in_array($_GET['profile'], $profiles)) ? $_GET['profile'] : NULL;
$options['creator'] = (!empty($_GET['creator']) && in_array($_GET['creator'], array_keys($creators))) ? $creators[$_GET['creator']] : NULL;
$options['collection'] = (!empty($_GET['property']) && in_array($_GET['property'], array_keys($properties))) ? $properties[$_GET['property']] : NULL;
$options['text'] = (!empty($_GET['text'])) ? $_GET['text'] : NULL;
$options['tag'] = (!empty($_GET['tag'])) ? $_GET['tag'] : NULL;
?>

<div class="pure-g">
  <div class="pure-u-1-1 1-box">
  <form action="index.php" method="post">
  <input class="pure-button" type="submit" name='change' value="<?php print $server; ?>" />
  </form>
</div>


<div class="pure-u-1-1 1-box">

<form action="index.php" method="get" class="pure-form pure-form-stacked">

    <fieldset>
        <legend>PMP Query</legend>
    
    <div class="pure-g">
    <div class="pure-u-1 pure-u-md-1-3">
        <select name="profile" class="pure-u-23-24">
            <option value=''>Profile:</option>
      <?php foreach($profiles as $v) {
        $selected = ($v == $profile) ? 'selected' : '';
        print "<option $selected value='$v'>$v</option>";
      } ?>
        </select>
    </div>
    
    <div class="pure-u-1 pure-u-md-1-3">
    <select name="creator" class="pure-u-23-24">
      <option value="">Creator:</option>
      <?php foreach($creators as $k => $v) {
        $selected = ($k == $creator) ? 'selected' : '';
        print "<option $selected value='$k'>$k</option>";
      } ?>
    </select>
    </div>

    <div class="pure-u-1 pure-u-md-1-3">
    <select name="property" class="pure-u-23-24">
      <option value="">Program:</option>
      <?php foreach($properties as $k => $v) {
        $selected = ($k == $property) ? 'selected' : '';
        print "<option $selected value='$k'>$k</option>";
      } ?>
    </select>
    </div>
    
    <div class="pure-u-1 pure-u-md-1-3">
    <input name="tag" type="text" placeholder="Tag(s)" value="<?php print $tag; ?>" class="pure-u-23-24">
    </div>
    
    <div class="pure-u-1 pure-u-md-1-3">
    <input name="text" type="text" placeholder="Search term(s)" value="<?php print $text; ?>" class="pure-u-23-24">
    </div>
    
    <div class="pure-u-1 pure-u-md-1-3">
    <input name="guid" type="text" placeholder="Enter a PMP GUID" value="" class="pure-u-23-24">
    </div>
    
    </div>
    <button type="submit" class="pure-button pure-button-primary">Submit</button>
    </fieldset>
</form>
</div>
</div>
<?php
$call = new PMPCall($creds[$server]['host'], $creds[$server]['client_id'], $creds[$server]['client_secret']);
$call->pull($options);
print "<div class='row'>" . krumo($call->query->results->fullDoc) . "</div>";
?>
<div class="pure-u-1-1 1-box">
<table class="pure-table"><thead><tr><th>TITLE</th><th>GUID</th><!--<th>VIEW</th><th>EDIT</th><th>DELETE</th>--><th>PROFILE</th><th>CREATOR</th><th>PUBLISHED</th></tr></thead><tbody>
<?php
if (!empty($call->query->results->docs)) {
  foreach($call->query->results->docs as $doc) {
    $date = date('D, M jS Y \a\t g:i a', strtotime($doc->attributes->published));
    $creator_href = $doc->links->creator[0]->href;
    $pieces = explode('/', $creator_href);
    $doc_creator = array_pop($pieces);
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

<?php
$pages = $call->query->results->fullDoc->links->navigation[0]->totalpages;
if ($pages > 1): ?>
  <div id ="pager">
  <?php
  $nav_params = $new_params;
  unset($nav_params['page']);
  $base = '/index.php';
  $query = '?' . http_build_query($nav_params) . '&page=';
  if ($page > 1) print '<a class="pure-button" href="' . $base . '">First</a>';
  if ($page > 1) print '<a class="pure-button" href="' . $base . $query . (int) ($page-1) . '">←PREV</a>';
  if ($page < $pages) print '<a class="pure-button" href="' . $base. $query . (int) ($page+1) . '"> NEXT→</a>';
  if ($page != $pages) print '<a class="pure-button" href="' . $base. $query . $pages . '">Last</a>';
  ?>
  </div>
<?php endif; ?>
</div>
</div>
</div>
  </body>
</html>
