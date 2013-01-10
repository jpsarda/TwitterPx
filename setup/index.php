<?php
ob_Start();
?>

<?php
require_once '../twitterpool/TwitterTokens.php';
require '../twitterpx/config.php';

$here = tmhUtilities::php_self();
session_start();

function outputError($tmhOAuth) {
  echo 'Error: ' . $tmhOAuth->response['response'] . PHP_EOL;
  tmhUtilities::pr($tmhOAuth);
}


$twitterTokens=new TwitterTokens;

// reset request?
if ( isset($_REQUEST['removetoken'])) {
  echo $_REQUEST['removetoken'].' removed from the pool';
  $twitterTokens->removeToken($_REQUEST['removetoken']);
  $twitterTokens->saveTokens();
} else if ( isset($_REQUEST['wipe'])) {
  session_destroy();
  header("Location: {$here}");

// already got some credentials stored?
} elseif ( isset($_SESSION['bl_access_token']) ) {
  $tmhOAuth->config['user_token']  = $_SESSION['bl_access_token']['oauth_token'];
  $tmhOAuth->config['user_secret'] = $_SESSION['bl_access_token']['oauth_token_secret'];

  $code = $tmhOAuth->request('GET', $tmhOAuth->url('1/account/verify_credentials'));
  if ($code == 200) {
    $resp = json_decode($tmhOAuth->response['response']);
    echo $resp->screen_name.' added to the pool';

    $twitterTokens->addToken($resp->screen_name,$_SESSION['bl_access_token']['oauth_token'],$_SESSION['bl_access_token']['oauth_token_secret']);
    $twitterTokens->saveTokens();

    session_destroy();
  } else {
    outputError($tmhOAuth);
  }
// we're being called back by Twitter
} elseif (isset($_REQUEST['oauth_verifier'])) {
  $tmhOAuth->config['user_token']  = $_SESSION['bl_oauth']['oauth_token'];
  $tmhOAuth->config['user_secret'] = $_SESSION['bl_oauth']['oauth_token_secret'];

  $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/access_token', ''), array(
    'oauth_verifier' => $_REQUEST['oauth_verifier']
  ));

  if ($code == 200) {
    $_SESSION['bl_access_token'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
    unset($_SESSION['bl_oauth']);
    header("Location: {$here}");
  } else {
    outputError($tmhOAuth);
  }
// start the OAuth dance
} elseif ( isset($_REQUEST['authenticate']) || isset($_REQUEST['authorize']) ) {
  $callback = isset($_REQUEST['oob']) ? 'oob' : $here;

  $params = array(
    'oauth_callback'     => $callback
  );

  if (isset($_REQUEST['force_write'])) :
    $params['x_auth_access_type'] = 'write';
  elseif (isset($_REQUEST['force_read'])) :
    $params['x_auth_access_type'] = 'read';
  endif;

  $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/request_token', ''), $params);

  if ($code == 200) {
    $_SESSION['bl_oauth'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
    $method = isset($_REQUEST['authenticate']) ? 'authenticate' : 'authorize';
    $force  = isset($_REQUEST['force']) ? '&force_login=1' : '';
    $authurl = $tmhOAuth->url("oauth/{$method}", '') .  "?oauth_token={$_SESSION['bl_oauth']['oauth_token']}{$force}";
    //echo '<p>To complete the OAuth flow follow this URL: <a href="'. $authurl . '">' . $authurl . '</a></p>';
    header("Location: {$authurl}");
  } else {
    outputError($tmhOAuth);
  }
}

?>
<h1>Add new twitter users to the pool</h1>
<ul>
  <li><a href="?authorize=1">Authorize Application</a></li>
  <li><a href="?authorize=1&amp;force=1">Authorize Application (force login)</a></li>
</ul>

<h1>Pool of twitter user tokens</h1>
<?php
$tokens=$twitterTokens->getTokens();
echo '<h2>'.count($tokens).' token(s) => '.(count($tokens)*300).' authenticated request(s) per hour</h2>';
foreach ($twitterTokens->getTokens() as $k => $v) {
  echo '<a href="http://twitter.com/'.$k.'">@'.$k.'</a> <a href="?removetoken='.$k.'">[REMOVE]</a> ';
}
?>

<?php
ob_end_flush();
?>